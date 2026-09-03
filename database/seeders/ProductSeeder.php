<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_slug' => 'solid-carbide-end-mills',
                'name' => 'NR-Demo 4枚刃 超硬エンドミル 6mm',
                'slug' => 'nr-demo-4-flute-end-mill-6mm',
                'sku' => 'NR-DEMO-EM-060',
                'summary' => '汎用スクエア形状を想定したポートフォリオ用デモモデルです。',
                'description' => '一般鋼の側面加工と溝加工を想定して作成した架空の製品データです。実在する製品やメーカー仕様を転載したものではありません。',
                'image_path' => '/images/products/nr-demo-4-flute-end-mill-6mm.webp',
                'specifications' => [
                    ['label' => '刃径', 'value' => '6 mm'],
                    ['label' => '刃数', 'value' => '4'],
                    ['label' => 'コーティング', 'value' => 'Demo AlCrN'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 四刃硬质合金立铣刀 6mm',
                    'summary' => '面向通用方肩加工场景设计的作品集演示型号。',
                    'description' => '这是为普通钢侧面加工和开槽加工场景原创的虚构产品数据，并非转载任何真实产品或制造商规格。',
                    'specifications' => [
                        ['label' => '刀具直径', 'value' => '6 mm'],
                        ['label' => '刃数', 'value' => '4'],
                        ['label' => '涂层', 'value' => '演示 AlCrN'],
                    ],
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'category_slug' => 'solid-carbide-end-mills',
                'name' => 'NR-Demo ボールエンドミル R3',
                'slug' => 'nr-demo-ball-end-mill-r3',
                'sku' => 'NR-DEMO-BE-R30',
                'summary' => '曲面仕上げ加工を想定したポートフォリオ用デモモデルです。',
                'description' => '金型の曲面仕上げを題材に作成した架空の製品データです。実際の切削条件や性能を保証するものではありません。',
                'image_path' => '/images/products/nr-demo-ball-end-mill-r3.webp',
                'specifications' => [
                    ['label' => 'ボール半径', 'value' => 'R3'],
                    ['label' => '刃数', 'value' => '2'],
                    ['label' => '全長', 'value' => '75 mm'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 球头立铣刀 R3',
                    'summary' => '面向曲面精加工场景设计的作品集演示型号。',
                    'description' => '这是以模具曲面精加工为主题原创的虚构产品数据，不代表或保证实际切削条件与性能。',
                    'specifications' => [
                        ['label' => '球头半径', 'value' => 'R3'],
                        ['label' => '刃数', 'value' => '2'],
                        ['label' => '总长', 'value' => '75 mm'],
                    ],
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'category_slug' => 'indexable-milling-tools',
                'name' => 'NR-Demo 肩削りカッタ 50mm',
                'slug' => 'nr-demo-shoulder-cutter-50mm',
                'sku' => 'NR-DEMO-MC-050',
                'summary' => '90度肩削り加工を想定したポートフォリオ用デモモデルです。',
                'description' => 'B2B 製品カタログの表示例として独自に作成した架空のカッターデータです。実在企業の型番や図面は使用していません。',
                'image_path' => '/images/products/nr-demo-shoulder-cutter-50mm.webp',
                'specifications' => [
                    ['label' => 'カッタ径', 'value' => '50 mm'],
                    ['label' => '刃数', 'value' => '4'],
                    ['label' => '取付方式', 'value' => 'アーバ式（Demo）'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 方肩铣刀 50mm',
                    'summary' => '面向 90° 方肩铣削场景设计的作品集演示型号。',
                    'description' => '这是为展示 B2B 产品目录而原创的虚构铣刀数据，未使用任何真实企业的型号或图纸。',
                    'specifications' => [
                        ['label' => '刀具直径', 'value' => '50 mm'],
                        ['label' => '刃数', 'value' => '4'],
                        ['label' => '安装方式', 'value' => '心轴式（演示）'],
                    ],
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'category_slug' => 'drilling-tools',
                'name' => 'NR-Demo 超硬ドリル 8mm',
                'slug' => 'nr-demo-carbide-drill-8mm',
                'sku' => 'NR-DEMO-DR-080',
                'summary' => '鋼材の穴あけ加工を想定したポートフォリオ用デモモデルです。',
                'description' => '問い合わせ導線を確認するために作成した架空のドリル製品データです。実際の加工能力や寿命を示すデータではありません。',
                'image_path' => '/images/products/nr-demo-carbide-drill-8mm.webp',
                'specifications' => [
                    ['label' => '刃径', 'value' => '8 mm'],
                    ['label' => '有効加工深さ', 'value' => '3×D（Demo）'],
                    ['label' => '内部給油', 'value' => '対応'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 硬质合金钻头 8mm',
                    'summary' => '面向钢材钻孔场景设计的作品集演示型号。',
                    'description' => '这是为验证询盘流程而原创的虚构钻头产品数据，不代表实际加工能力或使用寿命。',
                    'specifications' => [
                        ['label' => '钻头直径', 'value' => '8 mm'],
                        ['label' => '有效加工深度', 'value' => '3×D（演示）'],
                        ['label' => '内冷', 'value' => '支持'],
                    ],
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'category_slug' => 'drilling-tools',
                'name' => 'NR-Demo マシンリーマ 10mm',
                'slug' => 'nr-demo-machine-reamer-10mm',
                'sku' => 'NR-DEMO-RM-100',
                'summary' => '穴仕上げ加工を想定したポートフォリオ用デモモデルです。',
                'description' => '仕様表とカテゴリ絞り込みの検証用に独自作成した架空の製品データです。実在製品の寸法表は使用していません。',
                'image_path' => '/images/products/nr-demo-machine-reamer-10mm.webp',
                'specifications' => [
                    ['label' => '刃径', 'value' => '10 mm'],
                    ['label' => '刃数', 'value' => '6'],
                    ['label' => '公差表記', 'value' => 'Demo H7'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 机用铰刀 10mm',
                    'summary' => '面向孔精加工场景设计的作品集演示型号。',
                    'description' => '这是为验证规格表和分类筛选功能而原创的虚构产品数据，未使用任何真实产品的尺寸表。',
                    'specifications' => [
                        ['label' => '刀具直径', 'value' => '10 mm'],
                        ['label' => '刃数', 'value' => '6'],
                        ['label' => '公差标注', 'value' => '演示 H7'],
                    ],
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'category_slug' => 'turning-tools',
                'name' => 'NR-Demo 外径旋削ホルダ',
                'slug' => 'nr-demo-external-turning-holder',
                'sku' => 'NR-DEMO-TH-2525',
                'summary' => '外径旋削のカタログ表示を想定したポートフォリオ用デモモデルです。',
                'description' => '管理画面と公開ページの動作確認用に作成した架空のホルダーデータです。実在する型番やクランプ構造を再現していません。',
                'image_path' => '/images/products/nr-demo-external-turning-holder.webp',
                'specifications' => [
                    ['label' => 'シャンク寸法', 'value' => '25 × 25 mm'],
                    ['label' => '勝手', 'value' => '右勝手'],
                    ['label' => 'クランプ方式', 'value' => 'Demo clamp'],
                ],
                'translation' => [
                    'name' => 'NR-Demo 外圆车削刀杆',
                    'summary' => '面向外圆车削目录展示场景设计的作品集演示型号。',
                    'description' => '这是为验证管理后台和公开页面而原创的虚构刀杆数据，未复刻任何真实型号或夹紧结构。',
                    'specifications' => [
                        ['label' => '刀杆尺寸', 'value' => '25 × 25 mm'],
                        ['label' => '方向', 'value' => '右手'],
                        ['label' => '夹紧方式', 'value' => '演示夹紧'],
                    ],
                ],
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 60,
            ],
        ];

        foreach ($products as $product) {
            $translation = $product['translation'];
            $category = ProductCategory::query()
                ->where('slug', $product['category_slug'])
                ->sole();

            $sku = $product['sku'];

            unset($product['category_slug'], $product['translation']);

            $model = Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    ...$product,
                    'product_category_id' => $category->id,
                ],
            );

            $model->translations()->updateOrCreate(
                ['locale' => ProductTranslation::LOCALE_CHINESE],
                $translation,
            );
        }
    }
}
