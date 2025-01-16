<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use App\Models\DoctorInfo;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\Package;
use App\Models\Pet;
use App\Models\Order;
use App\Models\PetOwner;
use App\Models\Product;
use App\Models\Provider;
use App\Models\QrCode;
use App\Models\Reviews;
use App\Models\Service;
use App\Models\TrainerInfo;
use App\Models\Veterinarian;
use Illuminate\Database\Seeder;
use App\Models\Fav;
use App\Models\Cart;
use App\Models\Blogs;
use App\Models\Reminder;
use App\Models\LostPets;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Payment;
use App\Models\Card;
use App\Models\BoughtCoupon;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Code;
use App\Models\CodeUsage;
use App\Models\CouponUsage;
use App\Models\Collar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Seed permissions
        $permissions = Permission::factory()->count(7)->create();

        $roles = [
            'super admin',
            'manager',
            'employee',
            'trainee'
        ];

        foreach ($roles as $roleName) {
            $role = Role::factory()->create(['name' => $roleName]);
            Role::factory()->assignPermissions($role);
        }


        // Seed admin users
        $admins = Admin::factory()->count(10)->create();

        // Attach roles to admin users based on their roles
        foreach ($admins as $admin) {
            $role = Role::where('name', $admin->role)->first();
            $admin->roles()->attach($role);
        };


        // Ensure at least one package with status === 1
        Package::factory()->create(['status' => 1]);

        // Create additional packages
        $allPackages = Package::factory()->count(7)->create();

        // Filter packages with status === 1
        $packages = Package::where('status', 1)->get();


        Provider::factory()
            ->count(30)
            ->create();


        DoctorInfo::factory()
            ->count(10)
            ->create();

        TrainerInfo::factory()
            ->count(10)
            ->create();

        Blogs::factory()
            ->count(10)
            ->create();
        // Collection of all pets in system
        $pets = collect();

        // Iterate over oweners
        foreach (range(1, 10) as $i) {
            // Create data
            $current_owner_pets_factory = Pet::factory()
                ->count(fake()->numberBetween(2, 6));

            $invoice = Invoice::factory()
                ->count(fake()->numberBetween(2, 4));

            $pet_owner = PetOwner::factory()
                ->has($current_owner_pets_factory)
                ->has($invoice)
                ->create();

            echo "reached\n";

            // Store in variables
            $current_owner_pets = $pet_owner->pets;
            $pets->push(...$current_owner_pets);


            // Memberships
            $memberships_count = fake()->numberBetween(0, $current_owner_pets->count());

            if ($memberships_count === 1) {
                $package = $packages->random();

                Membership::factory()
                    ->fromPackage(
                        package: $package,
                        useFirstPrice: true,
                    )
                    ->create([
                        // 'pet_id' => fake()->randomElement($current_owner_pets)->id,
                        'pet_id' => $current_owner_pets->random(),
                        'package_id' => $package->id,
                    ]);
            } else if ($memberships_count > 1) {
                $package = $packages->random();

                $random_pets = $current_owner_pets->random($memberships_count);

                $random_pets->map(function (Pet $pet, int $key) use ($package) {
                    Membership::factory()
                        ->fromPackage(
                            package: $package,
                            useFirstPrice: $key === 0,
                        )
                        ->create([
                            // 'pet_id' => fake()->randomElement($current_owner_pets)->id,
                            'pet_id' => $pet,
                            'package_id' => $package->id,
                        ]);

                    // Generate QR codes for each pet
                    QrCode::factory()->forPet($pet->id)->create();
                });
            }
        }


        //create the collections
        $providers = Provider::get();
        $services = collect();
        $products = collect();
        $veterinarian = collect();

        // Filtering the providers
        $services_provider = $providers->filter(function (Provider $provider, int $key) {
            $provider_service_type = ['doctor', 'groomer', 'pet clinic', 'trainer'];
            return in_array($provider->type, $provider_service_type);
        });

        $products_provider = $providers->filter(function (Provider $provider, int $key) {
            $products_provider_type = ['pet shop', 'groomer', 'pet clinic'];
            return in_array($provider->type, $products_provider_type);
        });

        $clinic_provider = $providers->filter(function (Provider $provider, int $key) {
            return $provider->type == 'pet clinic';
        });

        //create the services assocciated with the filtered providers who provide products
        foreach ($services_provider as $current_services_provider) {

            // make services without the provider_id
            $current_service_factory = Service::factory()
                ->count(fake()->numberBetween(2, 4))
                ->make();

            // add the provider_id to the created services
            $current_services_provider->services()->saveMany($current_service_factory);

            // Store the created services in variable then push it to the collection
            $current_service = $current_services_provider->services;
            $services->push(...$current_service);
        }

        //create the products assocciated with the filtered providers who provide products
        foreach ($products_provider as $current_products_provider) {

            // make products without the provider_id
            $current_products_factory = Product::factory()
                ->count(fake()->numberBetween(2, 4))
                ->make();

            // add the provider_id to the created products
            $current_products_provider->products()->saveMany($current_products_factory);

            // Store the created products in variable then push it to the collection
            $current_product = $current_products_provider->products;
            $products->push(...$current_product);
        }

        //create the veterinarian assocciated with the filtered providers with type pet clinic
        foreach ($clinic_provider as $clinic) {

            // make veterinarian without the provider_id
            $current_veterinarian_factory = Veterinarian::factory()
                ->count(fake()->numberBetween(1, 3))
                ->make();

            // add the provider_id to the created veterinarian
            $clinic->veterinarians()->saveMany($current_veterinarian_factory);

            // Store the created veterinarian in variable then push it to the collection
            $current_veterinarians = $clinic->veterinarians;
            $veterinarian->push(...$current_veterinarians);
        }

        // Seed Favs with valid product_id or service_id
        PetOwner::all()->each(function ($petOwner) {
            Fav::factory()->count(2)->create([
                'pet_owner_id' => $petOwner->id,
            ]);
        });

        // Seed Cart with valid product_id only
        PetOwner::all()->each(function ($petOwner) {
            Cart::factory()->count(2)->create([
                'pet_owner_id' => $petOwner->id,
            ]);
        });


        // Print the results

        // Add review generation logic here
        $petOwners = PetOwner::all();
        $services = Service::all();
        $products = Product::all();

        foreach ($petOwners as $petOwner) {
            $numberOfReviews = fake()->numberBetween(1, 5);
            foreach (range(1, $numberOfReviews) as $i) {
                $serviceOrProduct = fake()->randomElement(['service', 'product']);
                $reviewData = [
                    'pet_owner_id' => $petOwner->id,
                    'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                    'rate' => fake()->numberBetween(0, 5),
                    'comment' => fake()->sentence(),
                ];

                if ($serviceOrProduct == 'service' && $services->isNotEmpty()) {
                    $reviewData['service_id'] = $services->random()->id;
                } elseif ($products->isNotEmpty()) {
                    $reviewData['product_id'] = $products->random()->id;
                }

                Reviews::create($reviewData);
            }
        }

        // Create reminders
        $petOwners = PetOwner::all();
        $pets = Pet::all(); // Assuming pet_id is optional and you might have some reminders without it
        $providers = Provider::all();
        foreach ($petOwners as $petOwner) {
            Reminder::factory()->count(3)->create([
                'pet_owner_id' => $petOwner->id,
                'pet_id' => $pets->isNotEmpty() ? $pets->random()->id : null,
                'provider_id' => $providers->isNotEmpty() ? $providers->random()->id : null,
            ]);
        }

        
        // Seed Favs with a random valid product_id, service_id, or provider_id

        PetOwner::all()->each(function ($petOwner) {
            Fav::factory()->count(2)->create([
                'pet_owner_id' => $petOwner->id,
            ]);
        });

        // Seed Cart with valid product_id only
        PetOwner::all()->each(function ($petOwner) {
            Cart::factory()->count(2)->create([
                'pet_owner_id' => $petOwner->id,
            ]);
        });

        // Seed LostPets with valid pet_owner_id
        PetOwner::all()->each(function ($petOwner) {
            LostPets::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create([
                    'pet_owner_id' => $petOwner->id,
                ]);
        });

        $petOwners = PetOwner::all();
        $pets = Pet::all(); // Assuming pet_id is optional and you might have some reminders without it
        $providers = Provider::all();
        foreach ($petOwners as $petOwner) {
            Reminder::factory()->count(3)->create([
                'pet_owner_id' => $petOwner->id,
                'pet_id' => $pets->isNotEmpty() ? $pets->random()->id : null,
                'provider_id' => $providers->isNotEmpty() ? $providers->random()->id : null,
            ]);
        }
        
        //Seed four tables 
        $petOwners = PetOwner::all();
        $providers = Provider::all();
        Coupon::factory()->count(20)->make()->each(function ($coupon) use ($providers) {
            $provider = $providers->random(); 
            $coupon->provider_id = $provider->id; 
            $coupon->title = $provider->name; 
            $coupon->image = $provider->profile_image; 
            $coupon->save(); 
        });
        
        Code::factory()->count(30)->create();
        $codes = Code::all();
        $coupons = Coupon::all();
        $petOwners->each(function ($petOwner) use ($codes) {
            if (fake()->boolean(50)) { 
                CodeUsage::factory()->count(2)->create([
                    'owner_id' => $petOwner->id,
                    'code_id' => $codes->random()->id, // Pick a random code ID
                ]);
            }
        });
        $petOwners->each(function ($petOwner) use ($coupons) {
            if (fake()->boolean(50)) { 
                CouponUsage::factory()->count(2)->create([
                    'owner_id' => $petOwner->id,
                    'coupon_id' => $coupons->random()->id, // Pick a random coupon ID
                ]);
            }
        });

        Collar::factory()->count(100)->create();
        

        Card::factory(10)->create();

        Order::factory(20)->create();
        
        $orders = Order::all();


        foreach ($orders as $order) {
            $metadata = json_decode($order->metadata, true); 
        
            // Initialize payment data
            $paymentData = [
                'provider_id' => null, // Default to null
            ];
        
            // Randomly choose between order, coupon, or package
            $paymentType = \Faker\Factory::create()->randomElement(['order', 'coupon', 'package']);
        
            if ($paymentType === 'order' && $order->id) {
                $paymentData['package_id'] = null; 
                $paymentData['coupon_id'] = null; 
                $paymentData['provider_id'] = $metadata['products'][0]['provider_id'] ?? null;
                $paymentData['order_id'] = $order->id;
                $paymentData['amount'] = $order->amount;
                $paymentData['discount_amount'] = $order->discount_amount;
                $paymentData['status'] = $order->status;
                $paymentData['pet_owner_id'] = $order->pet_owner_id; // Use the user ID from the order
            } elseif ($paymentType === 'coupon') {
                // Randomly generate a coupon ID
                $paymentData['coupon_id'] = Coupon::all()->random()->id;// Replace with your logic to get a random coupon ID
                $paymentData['order_id'] = null; // Ensure order_id is null
                $paymentData['package_id'] = null; // Ensure package_id is null
                $paymentData['pet_owner_id'] = PetOwner::factory()->create()->id; // Random pet owner
                $paymentData['amount'] = \Faker\Factory::create()->randomFloat(2, 10, 1000); // Random amount
                $paymentData['discount_amount'] = \Faker\Factory::create()->randomFloat(2, 0, 50); // Random discount
                $paymentData['status'] = 'completed'; // or set as needed
            } elseif ($paymentType === 'package') {
                // Randomly generate a package ID
                $paymentData['package_id'] = Package::all()->random()->id; // Replace with your logic to get a random package ID
                $paymentData['order_id'] = null; // Ensure order_id is null
                $paymentData['coupon_id'] = null; // Ensure coupon_id is null
                $paymentData['pet_owner_id'] = PetOwner::factory()->create()->id; // Random pet owner
                $paymentData['amount'] = \Faker\Factory::create()->randomFloat(2, 10, 1000); // Random amount
                $paymentData['discount_amount'] = \Faker\Factory::create()->randomFloat(2, 0, 50); // Random discount
                $paymentData['status'] = 'completed'; // or set as needed
            }
        
            // Create the payment
            Payment::factory()->create($paymentData);
        }
        

        BoughtCoupon::factory(10)->create();

        //print the collections
        echo "Created " . $pets->count() . " pets\n";
        echo "Created " . $services->count() . " services\n";
        echo "Created " . $products->count() . " products\n";
        echo "Created " . $veterinarian->count() . " veterinarians\n";
        echo "Seeded " . Fav::count() . " favorite items\n";
        echo "Seeded " . Cart::count() . " cart items\n";
    }
}
