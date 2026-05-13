<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expense_number',
        'category_id',
        'amount',
        'currency',
        'expense_date',
        'payment_method',
        'reference_number',
        'description',
        'status',
        'notes',
        'receipt_image',
        'recorded_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Boot the model and auto-generate expense number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (empty($expense->expense_number)) {
                $expense->expense_number = self::generateExpenseNumber();
            }
            if (empty($expense->recorded_by)) {
                $expense->recorded_by = auth()->id();
            }
            if (empty($expense->status)) {
                $expense->status = 'pending';
            }
        });
    }

    /**
     * Get the category that owns the expense.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the user who recorded the expense.
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the user who approved the expense.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate a unique expense number.
     * Format: EXP-YYYYMM-XXXX (e.g., EXP-202603-0001)
     */
    public static function generateExpenseNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "EXP-{$year}{$month}-";

        $lastExpense = self::withTrashed()
            ->where('expense_number', 'like', $prefix.'%')
            ->orderBy('expense_number', 'desc')
            ->first();

        if ($lastExpense) {
            $lastNumber = intval(substr($lastExpense->expense_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if expense is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if expense is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if expense is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if expense is paid.
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if expense can be approved.
     */
    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if expense can be edited.
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Check if expense can be deleted.
     */
    public function canBeDeleted()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Scope to only include pending expenses.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to only include approved expenses.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to only include paid expenses.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
}
