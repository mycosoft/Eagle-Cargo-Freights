<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShipmentEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions and role
        Permission::firstOrCreate(['name' => 'manage shipments', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo('manage shipments');
    }

    public function test_can_create_shipment_with_receiver_and_billing()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::factory()->create();
        $receiver = Client::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.shipments.store'), [
            'client_id' => $client->id,
            'receiver_id' => $receiver->id,
            'origin' => 'New York',
            'destination' => 'London',
            'shipment_type' => 'air',
            'shipping_cost' => 100.00,
            'insurance_value' => 10.00,
            'tax' => 5.00,
            'discount' => 2.00,
            'total_amount' => 113.00,
            'currency' => 'USD',
            'payment_method' => 'card',
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipments', [
            'client_id' => $client->id,
            'receiver_id' => $receiver->id,
            'shipping_cost' => 100.00,
            'tax' => 5.00,
            'discount' => 2.00,
            'total_amount' => 113.00,
            'currency' => 'USD',
        ]);
    }

    public function test_can_update_shipment_with_billing_details()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::factory()->create();
        $shipment = Shipment::create([
            'client_id' => $client->id,
            'tracking_number' => 'TEST-123',
            'origin' => 'A',
            'destination' => 'B',
            'shipment_type' => 'road',
        ]);

        $response = $this->actingAs($user)->put(route('admin.shipments.update', $shipment), [
            'client_id' => $client->id,
            'origin' => 'A',
            'destination' => 'B',
            'shipment_type' => 'road',
            'shipping_cost' => 200.00,
            'tax' => 20.00,
            'total_amount' => 220.00,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'shipping_cost' => 200.00,
            'tax' => 20.00,
            'total_amount' => 220.00,
        ]);
    }
}
