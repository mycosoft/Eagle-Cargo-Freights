<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eagle Cargo Freights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .bg-purple-900 {
            background-color: #4b0a82;
        }
        .text-purple-900 {
            color: #4b0a82;
        }
        .hover\:bg-purple-800:hover {
            background-color: #3d0769;
        }
        .focus\:ring-purple-900:focus {
            --tw-ring-color: #4b0a82;
        }
        .focus\:border-purple-900:focus {
            border-color: #4b0a82;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100" style="background-image: url('<?php echo e(asset('images/bg.png')); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg">
        <!-- Logo and Header -->
        <div class="text-center">
            <div class="flex justify-center mb-6">
                <!-- Assuming logo is in public/images/logo.png -->
                <img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="Eagle Cargo Freights Logo" class="h-40 w-auto object-contain">
            </div>
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to manage your shipments
            </p>
        </div>

        <!-- Session Status -->
        <?php if(session('status')): ?>
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" class="mt-8 space-y-6">
            <?php echo csrf_field(); ?>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address
                </label>
                <div class="mt-1">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="<?php echo e(old('email')); ?>" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-900 focus:border-purple-900 sm:text-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="Enter your email address"
                    >
                </div>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div class="mt-1">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-900 focus:border-purple-900 sm:text-sm <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="••••••••"
                    >
                </div>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="h-4 w-4 text-purple-900 focus:ring-purple-900 border-gray-300 rounded"
                    >
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <?php if(Route::has('password.request')): ?>
                    <div class="text-sm">
                        <a href="<?php echo e(route('password.request')); ?>" class="font-medium text-purple-900 hover:text-purple-800">
                            Forgot your password?
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-900 transition duration-150 ease-in-out"
                >
                    Sign in
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                &copy; <?php echo e(date('Y')); ?> Eagle Cargo Freights. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\eagle2\resources\views/auth/login.blade.php ENDPATH**/ ?>