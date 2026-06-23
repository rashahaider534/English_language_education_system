<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }
    public function Levels()
    {
        return $this->hasMany(Level::class);
    }
     public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot('started_at', 'completed_at');
    }
     public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'user_lessons')
            ->withPivot('status', 'started_at', 'completed_at');
    }
     public function words()
    {
        return $this->belongsToMany(Word::class, 'user_words')
            ->withPivot('status', 'added_at');
    }
     public function DailyChallenge()
    {
        return $this->belongsToMany(DailyChallenge::class, 'users_challenges')
            ->withPivot('progress', 'is_completed', 'completed_at', 'reward_claimed');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function levelExceptions()
    {
        return $this->hasMany(LevelException::class, 'user_id');
    }
    public function ratings()
    {
        return $this->hasMany(Rate::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function testAttempts()
    {
        return $this->hasMany(UserAttempt::class);
    }

    public function contactUsMessages()
    {
        return $this->hasMany(ContactUs::class);
    }
    public function placementTestscreate()
    {
        return $this->hasMany(PlacementTest::class, 'created_by');
    }
     public function LevelException()
    {
        return $this->hasMany(LevelException::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
