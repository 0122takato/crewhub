<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // 経験
            ['name' => '接客経験', 'category' => '経験'],
            ['name' => 'イベント運営経験', 'category' => '経験'],
            ['name' => '販売経験', 'category' => '経験'],
            ['name' => 'リーダー経験', 'category' => '経験'],
            ['name' => '受付経験', 'category' => '経験'],

            // 言語
            ['name' => '英語対応可', 'category' => '言語'],
            ['name' => '中国語対応可', 'category' => '言語'],
            ['name' => '韓国語対応可', 'category' => '言語'],

            // 資格
            ['name' => '普通自動車免許', 'category' => '資格'],
            ['name' => 'フォークリフト免許', 'category' => '資格'],
            ['name' => '警備員資格', 'category' => '資格'],
            ['name' => '調理師免許', 'category' => '資格'],
            ['name' => '食品衛生責任者', 'category' => '資格'],

            // その他
            ['name' => 'PC操作可', 'category' => 'その他'],
            ['name' => '力仕事可', 'category' => 'その他'],
            ['name' => '早朝勤務可', 'category' => 'その他'],
            ['name' => '深夜勤務可', 'category' => 'その他'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill['name']], $skill);
        }
    }
}
