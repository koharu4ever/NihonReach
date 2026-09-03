<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '超硬エンドミル',
                'slug' => 'solid-carbide-end-mills',
                'description' => '一般鋼から高硬度材までの切削を想定したデモ製品カテゴリです。',
                'translation' => [
                    'name' => '硬质合金立铣刀',
                    'description' => '这是一个演示产品分类，设定用于从普通钢到高硬度材料的切削加工。',
                ],
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => '刃先交換式フライス工具',
                'slug' => 'indexable-milling-tools',
                'description' => '交換式インサートを使用するフライス加工向けのデモ製品カテゴリです。',
                'translation' => [
                    'name' => '可转位铣削刀具',
                    'description' => '这是一个面向使用可更换刀片进行铣削加工的演示产品分类。',
                ],
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => '穴あけ工具',
                'slug' => 'drilling-tools',
                'description' => 'ドリル加工と穴仕上げ加工を想定したデモ製品カテゴリです。',
                'translation' => [
                    'name' => '孔加工刀具',
                    'description' => '这是一个面向钻孔和孔精加工的演示产品分类。',
                ],
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => '旋削工具',
                'slug' => 'turning-tools',
                'description' => '外径・内径旋削を想定したデモ製品カテゴリです。',
                'translation' => [
                    'name' => '车削刀具',
                    'description' => '这是一个面向外圆和内孔车削的演示产品分类。',
                ],
                'is_active' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($categories as $category) {
            $translation = $category['translation'];
            unset($category['translation']);

            $model = ProductCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );

            $model->translations()->updateOrCreate(
                ['locale' => ProductCategoryTranslation::LOCALE_CHINESE],
                $translation,
            );
        }
    }
}
