<?php

namespace Database\Seeders;

use App\Models\MeasurementField;
use App\Models\MeasurementTemplate;
use App\Models\Product;
use App\Models\ProductMaterial;
use App\Models\ProductVariant;
use App\Models\Service;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMeasurementFields();
        $this->seedMaterials();
        $this->seedServices();
        $this->seedReadyMadeProducts();
        $this->seedBespokeProducts();
        $this->seedEmbroideryProducts();

        $this->command->info('Test data seeded successfully.');
    }

    // ── Measurement Fields ─────────────────────────────────────────────────────

    private function seedMeasurementFields(): void
    {
        $fields = [
            ['name' => 'chest',          'label' => 'Chest'],
            ['name' => 'waist',          'label' => 'Waist'],
            ['name' => 'hips',           'label' => 'Hips'],
            ['name' => 'shoulder',       'label' => 'Shoulder Width'],
            ['name' => 'sleeve_length',  'label' => 'Sleeve Length'],
            ['name' => 'neck',           'label' => 'Neck'],
            ['name' => 'trouser_length', 'label' => 'Trouser Length'],
            ['name' => 'inseam',         'label' => 'Inseam'],
            ['name' => 'thigh',          'label' => 'Thigh'],
            ['name' => 'caftan_length',  'label' => 'Caftan Length'],
            ['name' => 'agbada_length',  'label' => 'Agbada Length'],
            ['name' => 'dress_length',   'label' => 'Dress Length'],
            ['name' => 'bust',           'label' => 'Bust'],
            ['name' => 'top_length',     'label' => 'Top Length'],
            ['name' => 'gown_length',    'label' => 'Gown Length'],
            ['name' => 'ankle',          'label' => 'Ankle'],
            ['name' => 'knee',           'label' => 'Knee'],
            ['name' => 'cap_size',       'label' => 'Cap Size'],
        ];

        foreach ($fields as $field) {
            MeasurementField::firstOrCreate(
                ['name' => $field['name']],
                ['label' => $field['label'], 'is_system' => true, 'is_active' => true]
            );
        }

        $this->command->info('  ✓ Measurement fields');
    }

    // ── Materials ──────────────────────────────────────────────────────────────

    private function seedMaterials(): void
    {
        $materials = [
            ['name' => 'Plain White Cotton',  'unit' => 'yards',  'stock_quantity' => 50],
            ['name' => 'Ankara Print Fabric', 'unit' => 'yards',  'stock_quantity' => 80],
            ['name' => 'Aso-Oke (Blue)',      'unit' => 'yards',  'stock_quantity' => 30],
            ['name' => 'Aso-Oke (Gold)',      'unit' => 'yards',  'stock_quantity' => 25],
            ['name' => 'Lace Fabric (White)', 'unit' => 'yards',  'stock_quantity' => 20],
            ['name' => 'Lace Fabric (Cream)', 'unit' => 'yards',  'stock_quantity' => 20],
            ['name' => 'Chiffon (Black)',     'unit' => 'yards',  'stock_quantity' => 40],
            ['name' => 'Senator Material',   'unit' => 'yards',  'stock_quantity' => 35],
            ['name' => 'Guinea Brocade',     'unit' => 'yards',  'stock_quantity' => 25],
            ['name' => 'Thread (White)',     'unit' => 'spools', 'stock_quantity' => 100],
            ['name' => 'Thread (Black)',     'unit' => 'spools', 'stock_quantity' => 100],
            ['name' => 'Thread (Assorted)', 'unit' => 'spools', 'stock_quantity' => 60],
            ['name' => 'Buttons (White)',   'unit' => 'packs',  'stock_quantity' => 50],
            ['name' => 'Buttons (Gold)',    'unit' => 'packs',  'stock_quantity' => 30],
            ['name' => 'Zipper (Invisible)','unit' => 'pieces', 'stock_quantity' => 80],
            ['name' => 'Zipper (Regular)', 'unit' => 'pieces', 'stock_quantity' => 80],
            ['name' => 'Lining Fabric',    'unit' => 'yards',  'stock_quantity' => 30],
            ['name' => 'Shoulder Pad',     'unit' => 'pairs',  'stock_quantity' => 40],
            ['name' => 'Interfacing',      'unit' => 'yards',  'stock_quantity' => 20],
            ['name' => 'Embroidery Thread','unit' => 'spools', 'stock_quantity' => 80],
        ];

        foreach ($materials as $m) {
            Product::firstOrCreate(
                ['name' => $m['name'], 'is_material' => true],
                [
                    'slug'            => 'mat-' . \Illuminate\Support\Str::slug($m['name']),
                    'unit'            => $m['unit'],
                    'stock_quantity'  => $m['stock_quantity'],
                    'price'           => 0,
                    'is_active'       => true,
                    'is_material'     => true,
                    'is_published'    => false,
                    'production_type' => 'ready_made',
                    'product_type'    => 'accessory',
                ]
            );
        }

        $this->command->info('  ✓ Materials (as products)');
    }

    // ── Services ───────────────────────────────────────────────────────────────

    private function seedServices(): void
    {
        $services = [
            ['name' => 'Trouser Hem',           'base_price' => 1500,  'category' => 'alteration',    'sort_order' => 1],
            ['name' => 'Shirt Shortening',      'base_price' => 2000,  'category' => 'alteration',    'sort_order' => 2],
            ['name' => 'Zip Replacement',       'base_price' => 2500,  'category' => 'alteration',    'sort_order' => 3],
            ['name' => 'Side Taking-in',        'base_price' => 3000,  'category' => 'alteration',    'sort_order' => 4],
            ['name' => 'General Dry Cleaning',  'base_price' => 3500,  'category' => 'dry_cleaning',  'sort_order' => 5],
            ['name' => 'Suit Dry Cleaning',     'base_price' => 6000,  'category' => 'dry_cleaning',  'sort_order' => 6],
            ['name' => 'Lace/Aso-oke Washing',  'base_price' => 4000,  'category' => 'dry_cleaning',  'sort_order' => 7],
            ['name' => 'Express Delivery',      'base_price' => 2000,  'category' => 'delivery',      'sort_order' => 8],
            ['name' => 'Monogram Embroidery',   'base_price' => 5000,  'category' => 'embroidery',    'sort_order' => 9],
            ['name' => 'Logo Printing',         'base_price' => 4500,  'category' => 'printing',      'sort_order' => 10],
        ];

        foreach ($services as $s) {
            Service::firstOrCreate(
                ['name' => $s['name']],
                [
                    'base_price' => $s['base_price'],
                    'category'   => $s['category'],
                    'sort_order' => $s['sort_order'],
                    'is_active'  => true,
                ]
            );
        }

        $this->command->info('  ✓ Services');
    }

    // ── Ready-Made Products ────────────────────────────────────────────────────

    private function seedReadyMadeProducts(): void
    {
        $readyMade = [
            [
                'name'     => 'Classic White Shirt',
                'price'    => 12000,
                'category' => 'ready_made',
                'sort_order' => 1,
                'variants' => [
                    ['variant_type' => 'size', 'variant_value' => 'S',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'M',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'L',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'XL',  'price_adjustment' => 1000],
                    ['variant_type' => 'size', 'variant_value' => 'XXL', 'price_adjustment' => 2000],
                ],
            ],
            [
                'name'     => 'Ankara Print Shirt',
                'price'    => 18000,
                'category' => 'ready_made',
                'sort_order' => 2,
                'variants' => [
                    ['variant_type' => 'size', 'variant_value' => 'S',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'M',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'L',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'XL', 'price_adjustment' => 1000],
                ],
            ],
            [
                'name'     => 'Chiffon Blouse',
                'price'    => 15000,
                'category' => 'ready_made',
                'sort_order' => 3,
                'variants' => [
                    ['variant_type' => 'color', 'variant_value' => 'Black',  'price_adjustment' => 0],
                    ['variant_type' => 'color', 'variant_value' => 'White',  'price_adjustment' => 0],
                    ['variant_type' => 'color', 'variant_value' => 'Nude',   'price_adjustment' => 0],
                    ['variant_type' => 'color', 'variant_value' => 'Maroon', 'price_adjustment' => 500],
                ],
            ],
            [
                'name'     => 'Casual Trousers',
                'price'    => 14000,
                'category' => 'ready_made',
                'sort_order' => 4,
                'variants' => [
                    ['variant_type' => 'size', 'variant_value' => '30',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => '32',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => '34',  'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => '36',  'price_adjustment' => 1000],
                    ['variant_type' => 'size', 'variant_value' => '38',  'price_adjustment' => 2000],
                ],
            ],
            [
                'name'     => 'Leather Belt',
                'price'    => 5000,
                'category' => 'accessory',
                'sort_order' => 5,
                'variants' => [],
            ],
            [
                'name'     => 'Ankara Bag',
                'price'    => 8500,
                'category' => 'accessory',
                'sort_order' => 6,
                'variants' => [
                    ['variant_type' => 'color', 'variant_value' => 'Blue',   'price_adjustment' => 0],
                    ['variant_type' => 'color', 'variant_value' => 'Orange', 'price_adjustment' => 0],
                ],
            ],
        ];

        foreach ($readyMade as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'product_type'    => 'ready_made',
                    'production_type' => null,
                    'stock_quantity'  => 20,
                    'is_active'       => true,
                    'is_embroidery'   => false,
                ])
            );

            foreach ($variants as $v) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'variant_type' => $v['variant_type'], 'variant_value' => $v['variant_value']],
                    ['price_adjustment' => $v['price_adjustment'], 'is_active' => true]
                );
            }
        }

        $this->command->info('  ✓ Ready-made products');
    }

    // ── Bespoke / Production Products ─────────────────────────────────────────

    private function seedBespokeProducts(): void
    {
        $fields = MeasurementField::pluck('id', 'name');

        $bespokeProducts = [
            [
                'name'        => 'Men\'s Senator Suit',
                'price'       => 65000,
                'category'    => 'tailoring',
                'sort_order'  => 10,
                'path'        => 'sewing_only',
                'measurements'=> ['chest', 'waist', 'hips', 'shoulder', 'sleeve_length', 'neck', 'trouser_length', 'inseam'],
                'materials'   => [
                    ['name' => 'Senator Material',  'quantity' => 4.5],
                    ['name' => 'Lining Fabric',     'quantity' => 2.0],
                    ['name' => 'Thread (Assorted)', 'quantity' => 2],
                    ['name' => 'Buttons (Gold)',    'quantity' => 1],
                    ['name' => 'Shoulder Pad',      'quantity' => 1],
                    ['name' => 'Interfacing',       'quantity' => 0.5],
                ],
            ],
            [
                'name'        => 'Men\'s Agbada',
                'price'       => 95000,
                'category'    => 'tailoring',
                'sort_order'  => 11,
                'path'        => 'sewing_embroidery',
                'measurements'=> ['chest', 'waist', 'shoulder', 'sleeve_length', 'agbada_length', 'trouser_length'],
                'materials'   => [
                    ['name' => 'Guinea Brocade',     'quantity' => 8.0],
                    ['name' => 'Thread (White)',     'quantity' => 3],
                    ['name' => 'Embroidery Thread',  'quantity' => 4],
                ],
            ],
            [
                'name'        => 'Ladies\' Kaftan',
                'price'       => 45000,
                'category'    => 'tailoring',
                'sort_order'  => 12,
                'path'        => 'sewing_only',
                'measurements'=> ['bust', 'waist', 'hips', 'shoulder', 'caftan_length', 'sleeve_length'],
                'materials'   => [
                    ['name' => 'Ankara Print Fabric', 'quantity' => 3.5],
                    ['name' => 'Thread (Assorted)',   'quantity' => 2],
                    ['name' => 'Zipper (Invisible)',  'quantity' => 1],
                ],
            ],
            [
                'name'        => 'Ladies\' Lace Gown',
                'price'       => 75000,
                'category'    => 'tailoring',
                'sort_order'  => 13,
                'path'        => 'sewing_only',
                'measurements'=> ['bust', 'waist', 'hips', 'shoulder', 'gown_length', 'sleeve_length'],
                'materials'   => [
                    ['name' => 'Lace Fabric (White)',  'quantity' => 4.0],
                    ['name' => 'Lining Fabric',        'quantity' => 3.0],
                    ['name' => 'Thread (White)',       'quantity' => 2],
                    ['name' => 'Zipper (Invisible)',   'quantity' => 1],
                    ['name' => 'Buttons (White)',      'quantity' => 1],
                ],
            ],
            [
                'name'        => 'Ankara Two-Piece',
                'price'       => 38000,
                'category'    => 'tailoring',
                'sort_order'  => 14,
                'path'        => 'sewing_only',
                'measurements'=> ['bust', 'waist', 'hips', 'top_length', 'trouser_length'],
                'materials'   => [
                    ['name' => 'Ankara Print Fabric', 'quantity' => 4.0],
                    ['name' => 'Thread (Assorted)',   'quantity' => 2],
                    ['name' => 'Zipper (Regular)',    'quantity' => 1],
                ],
            ],
            [
                'name'        => 'Men\'s Native Shirt',
                'price'       => 28000,
                'category'    => 'tailoring',
                'sort_order'  => 15,
                'path'        => 'sewing_only',
                'measurements'=> ['chest', 'waist', 'shoulder', 'sleeve_length', 'neck', 'top_length'],
                'materials'   => [
                    ['name' => 'Plain White Cotton',  'quantity' => 2.5],
                    ['name' => 'Thread (White)',      'quantity' => 1],
                    ['name' => 'Buttons (White)',     'quantity' => 1],
                ],
            ],
        ];

        $materials = Product::where('is_material', true)->pluck('id', 'name');

        foreach ($bespokeProducts as $data) {
            $measurementNames = $data['measurements'];
            $bom              = $data['materials'];
            unset($data['measurements'], $data['materials'], $data['path']);

            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'product_type'               => 'garment',
                    'production_type'            => 'production',
                    'stock_quantity'             => 0,
                    'is_active'                  => true,
                    'is_embroidery'              => false,
                    'estimated_production_hours' => 24,
                ])
            );

            // Measurement template
            if (! $product->measurementTemplate) {
                $fieldIds = collect($measurementNames)
                    ->map(fn ($name) => $fields[$name] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                MeasurementTemplate::create([
                    'product_id' => $product->id,
                    'fields'     => $fieldIds,
                ]);
            }

            // Bill of materials
            foreach ($bom as $b) {
                $materialId = $materials[$b['name']] ?? null;
                if (! $materialId) continue;

                ProductMaterial::firstOrCreate(
                    ['product_id' => $product->id, 'material_id' => $materialId],
                    ['quantity' => $b['quantity']]
                );
            }
        }

        $this->command->info('  ✓ Bespoke products');
    }

    // ── Embroidery Products ────────────────────────────────────────────────────

    private function seedEmbroideryProducts(): void
    {
        $embroideryProducts = [
            [
                'name'       => 'Custom Polo Shirt (Embroidered)',
                'price'      => 22000,
                'category'   => 'embroidery',
                'sort_order' => 20,
                'variants'   => [
                    ['variant_type' => 'size', 'variant_value' => 'S',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'M',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'L',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'XL',  'price_adjustment' => 1500],
                    ['variant_type' => 'size', 'variant_value' => 'XXL', 'price_adjustment' => 2500],
                ],
            ],
            [
                'name'       => 'Corporate T-Shirt (Printed)',
                'price'      => 8500,
                'category'   => 'printing',
                'sort_order' => 21,
                'variants'   => [
                    ['variant_type' => 'size', 'variant_value' => 'S',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'M',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'L',   'price_adjustment' => 0],
                    ['variant_type' => 'size', 'variant_value' => 'XL',  'price_adjustment' => 500],
                    ['variant_type' => 'size', 'variant_value' => 'XXL', 'price_adjustment' => 1000],
                ],
            ],
        ];

        foreach ($embroideryProducts as $data) {
            $variants    = $data['variants'];
            $productType = $data['category'];
            unset($data['variants']);

            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'product_type'    => $productType,
                    'production_type' => 'production',
                    'stock_quantity'  => 0,
                    'is_active'       => true,
                    'is_embroidery'   => $productType === 'embroidery',
                ])
            );

            foreach ($variants as $v) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'variant_type' => $v['variant_type'], 'variant_value' => $v['variant_value']],
                    ['price_adjustment' => $v['price_adjustment'], 'is_active' => true]
                );
            }
        }

        $this->command->info('  ✓ Embroidery/printing products');
    }
}
