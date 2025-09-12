<?php
namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        $types = ['Request', 'Diet Plan', 'Workout Plan', 'Profile', 'Settings', 'Feedback', 'Attendance', 'Payment', 'Chat', 'Membership'];

        return [
            'user_id' => User::factory(),           // creates a new user if needed
            'title'   => $this->faker->sentence(3), // random title with 5 words
            'message' => $this->faker->paragraph,   // random message
            'type'    => $this->faker->randomElement($types),
            'is_read' => $this->faker->boolean(20), // 20% chance read
        ];
    }
}
