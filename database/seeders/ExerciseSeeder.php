<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // Chest
            [
                'name'         => 'Bench Press',
                'muscle_group' => 'Chest',
                'description'  => 'A compound exercise targeting chest, shoulders, and triceps.',
                'video_link'   => 'https://www.youtube.com/watch?v=rT7DgCr-3pg',
            ],
            [
                'name'         => 'Incline Dumbbell Press',
                'muscle_group' => 'Chest',
                'description'  => 'Press dumbbells upward on a 30–45° incline bench to target upper chest.',
                'video_link'   => 'https://www.youtube.com/watch?v=8iPEnn-ltC8',
            ],
            [
                'name'         => 'Chest Dips',
                'muscle_group' => 'Chest',
                'description'  => 'Lean forward on parallel bars and dip down, focusing on chest activation.',
                'video_link'   => 'https://www.youtube.com/watch?v=2z8JmcrW-As',
            ],
            [
                'name'         => 'Cable Flys',
                'muscle_group' => 'Chest',
                'description'  => 'Using a cable machine, bring handles together in a hugging motion.',
                'video_link'   => 'https://www.youtube.com/watch?v=taI4XduLpTk',
            ],
            [
                'name'         => 'Push-ups',
                'muscle_group' => 'Chest',
                'description'  => 'Standard push-ups focusing on full range of motion.',
                'video_link'   => 'https://www.youtube.com/watch?v=IODxDxX7oi4',
            ],

            // Legs
            [
                'name'         => 'Barbell Squats',
                'muscle_group' => 'Legs',
                'description'  => 'Place barbell on shoulders and squat to parallel.',
                'video_link'   => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
            ],
            [
                'name'         => 'Leg Press',
                'muscle_group' => 'Legs',
                'description'  => 'Push weight away using leg press machine.',
                'video_link'   => 'https://www.youtube.com/watch?v=IZxyjW7MPJQ',
            ],
            [
                'name'         => 'Lunges',
                'muscle_group' => 'Legs',
                'description'  => 'Step forward and lower knee towards ground.',
                'video_link'   => 'https://www.youtube.com/watch?v=QOVaHwm-Q6U',
            ],
            [
                'name'         => 'Romanian Deadlifts',
                'muscle_group' => 'Legs',
                'description'  => 'Hinge at the hips with slight knee bend, lowering barbell.',
                'video_link'   => 'https://www.youtube.com/watch?v=2SHsk9AzdjA',
            ],
            [
                'name'         => 'Calf Raises',
                'muscle_group' => 'Legs',
                'description'  => 'Stand and raise heels off ground, squeeze at top.',
                'video_link'   => 'https://www.youtube.com/watch?v=YMmgqO8Jo-k',
            ],

            // Back
            [
                'name'         => 'Pull-ups',
                'muscle_group' => 'Back',
                'description'  => 'Bodyweight exercise strengthening back and biceps.',
                'video_link'   => 'https://www.youtube.com/watch?v=eGo4IYlbE5g',
            ],
            [
                'name'         => 'Bent-over Barbell Rows',
                'muscle_group' => 'Back',
                'description'  => 'Pull barbell to waist with slight torso incline.',
                'video_link'   => 'https://www.youtube.com/watch?v=vT2GjY_Umpw',
            ],
            [
                'name'         => 'Lat Pulldown',
                'muscle_group' => 'Back',
                'description'  => 'Use cable machine to pull bar down towards chest.',
                'video_link'   => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
            ],
            [
                'name'         => 'Seated Cable Row',
                'muscle_group' => 'Back',
                'description'  => 'Pull cable handle to torso while seated.',
                'video_link'   => 'https://www.youtube.com/watch?v=GZbfZ033f74',
            ],
            [
                'name'         => 'Deadlift',
                'muscle_group' => 'Back',
                'description'  => 'A full-body exercise focusing on the posterior chain.',
                'video_link'   => 'https://www.youtube.com/watch?v=op9kVnSso6Q',
            ],

            // Shoulders
            [
                'name'         => 'Overhead Press',
                'muscle_group' => 'Shoulders',
                'description'  => 'Press barbell overhead while engaging core.',
                'video_link'   => 'https://www.youtube.com/watch?v=qEwKCR5JCog',
            ],
            [
                'name'         => 'Lateral Raises',
                'muscle_group' => 'Shoulders',
                'description'  => 'Raise dumbbells sideways at shoulder height.',
                'video_link'   => 'https://www.youtube.com/watch?v=3VcKaXpzqRo',
            ],
            [
                'name'         => 'Front Raises',
                'muscle_group' => 'Shoulders',
                'description'  => 'Raise dumbbells in front to shoulder level.',
                'video_link'   => 'https://www.youtube.com/watch?v=-t7fuZ0KhDA',
            ],
            [
                'name'         => 'Arnold Press',
                'muscle_group' => 'Shoulders',
                'description'  => 'Dumbbell press with rotational movement.',
                'video_link'   => 'https://www.youtube.com/watch?v=vj2w851ZHRM',
            ],
            [
                'name'         => 'Face Pulls',
                'muscle_group' => 'Shoulders',
                'description'  => 'Pull rope towards face to target rear delts.',
                'video_link'   => 'https://www.youtube.com/watch?v=rep-qVOkqgk',
            ],

            // Abs
            [
                'name'         => 'Crunches',
                'muscle_group' => 'Abs',
                'description'  => 'Lie down and crunch upper body towards knees.',
                'video_link'   => 'https://www.youtube.com/watch?v=Xyd_fa5zoEU',
            ],
            [
                'name'         => 'Leg Raises',
                'muscle_group' => 'Abs',
                'description'  => 'Raise legs upward while lying on back.',
                'video_link'   => 'https://www.youtube.com/watch?v=l4kQd9eWclE',
            ],
            [
                'name'         => 'Plank',
                'muscle_group' => 'Abs',
                'description'  => 'Hold a straight body position on elbows and toes.',
                'video_link'   => 'https://www.youtube.com/watch?v=pSHjTRCQxIw',
            ],
            [
                'name'         => 'Russian Twists',
                'muscle_group' => 'Abs',
                'description'  => 'Twist torso side-to-side holding a weight.',
                'video_link'   => 'https://www.youtube.com/watch?v=wkD8rjkodUI',
            ],
            [
                'name'         => 'Mountain Climbers',
                'muscle_group' => 'Abs',
                'description'  => 'Drive knees alternately towards chest in plank position.',
                'video_link'   => 'https://www.youtube.com/watch?v=nmwgirgXLYM',
            ],

            // Triceps (Tryshape)
            [
                'name'         => 'Tricep Dips',
                'muscle_group' => 'Triceps',
                'description'  => 'Dip down on bars focusing on tricep activation.',
                'video_link'   => 'https://www.youtube.com/watch?v=6kALZikXxLc',
            ],
            [
                'name'         => 'Overhead Tricep Extension',
                'muscle_group' => 'Triceps',
                'description'  => 'Extend dumbbell behind head and back up.',
                'video_link'   => 'https://www.youtube.com/watch?v=_gsUck-7M74',
            ],
            [
                'name'         => 'Close-grip Bench Press',
                'muscle_group' => 'Triceps',
                'description'  => 'Bench press with hands closer for tricep focus.',
                'video_link'   => 'https://www.youtube.com/watch?v=ziPLOxv9g3E',
            ],
            [
                'name'         => 'Cable Pushdown',
                'muscle_group' => 'Triceps',
                'description'  => 'Push cable rope/bar down until arms extended.',
                'video_link'   => 'https://www.youtube.com/watch?v=2-LAMcpzODU',
            ],
            [
                'name'         => 'Tricep Kickbacks',
                'muscle_group' => 'Triceps',
                'description'  => 'Extend dumbbell backwards with elbows tucked.',
                'video_link'   => 'https://www.youtube.com/watch?v=6SS6K3lAwZ8',
            ],

            // Biceps (Byshape)
            [
                'name'         => 'Barbell Curl',
                'muscle_group' => 'Biceps',
                'description'  => 'Curl barbell upward focusing on bicep contraction.',
                'video_link'   => 'https://www.youtube.com/watch?v=kwG2ipFRgfo',
            ],
            [
                'name'         => 'Hammer Curl',
                'muscle_group' => 'Biceps',
                'description'  => 'Neutral grip curl with dumbbells.',
                'video_link'   => 'https://www.youtube.com/watch?v=zC3nLlEvin4',
            ],
            [
                'name'         => 'Concentration Curl',
                'muscle_group' => 'Biceps',
                'description'  => 'Curl dumbbell while elbow braced on thigh.',
                'video_link'   => 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo',
            ],
            [
                'name'         => 'Cable Curl',
                'muscle_group' => 'Biceps',
                'description'  => 'Curl cable handle towards shoulders.',
                'video_link'   => 'https://www.youtube.com/watch?v=1YhAgaV9bBQ',
            ],
            [
                'name'         => 'Chin-ups',
                'muscle_group' => 'Biceps',
                'description'  => 'Pull body upward with palms facing you.',
                'video_link'   => 'https://www.youtube.com/watch?v=brhRXlOhsAM',
            ],
        ];

        foreach ($exercises as $exercise) {
            DB::table('exercises')->insert([
                'name'         => $exercise['name'],
                'muscle_group' => $exercise['muscle_group'],
                'description'  => $exercise['description'],
                'video_link'   => $exercise['video_link'],
                'default_sets' => 3,
                'default_reps' => 10,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

}
