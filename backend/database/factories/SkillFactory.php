<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        $skills = [
            ['name' => '接客経験', 'category' => '経験'],
            ['name' => 'イベント運営経験', 'category' => '経験'],
            ['name' => '英語対応可', 'category' => '言語'],
            ['name' => '中国語対応可', 'category' => '言語'],
            ['name' => '普通自動車免許', 'category' => '資格'],
            ['name' => 'フォークリフト免許', 'category' => '資格'],
            ['name' => '警備員資格', 'category' => '資格'],
            ['name' => '調理師免許', 'category' => '資格'],
        ];

        $skill = fake()->randomElement($skills);

        return [
            'name' => $skill['name'],
            'category' => $skill['category'],
        ];
    }
}
