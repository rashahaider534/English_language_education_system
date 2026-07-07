<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public function guardName(): array
    {
        return ['web', 'api'];
    }
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'fcm_token',
        'email_verified_at'
    ];

    public function studentProfile():HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile():HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }
    public function levels()
    {
        return $this->belongsToMany(Level::class)
            ->using(UserLevel::class)
            ->withPivot(
                'status',
                'enrolled_at',
                'completed_at'
            );
    }

    public function userLevels():HasMany
    {
        return $this->hasMany(UserLevel::class);
    }
     public function StudentCourses():BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot('status','started_at', 'completed_at');
    }

    public function TeacherCourses():HasMany
    {
        return $this->hasMany(Course::class);
    }
     public function lessons():BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'user_lessons')
            ->withPivot('status', 'started_at', 'completed_at');
    }
     public function words():BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'user_words')
            ->withPivot('status', 'added_at');
    }
     public function DailyChallenge():BelongsToMany
    {
        return $this->belongsToMany(DailyChallenge::class, 'users_challenges' , 'user_id' , 'daily_challenge_id')
            ->withPivot('progress', 'is_completed', 'completed_at', 'reward_claimed','reward_claimed_at');
    }

    public function payments():HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function levelExceptions():HasMany
    {
        return $this->hasMany(LevelException::class, 'user_id');
    }
    public function ratings():HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function testAttempts():HasMany
    {
        return $this->hasMany(UserAttempt::class);
    }

    public function contactUsMessages():HasMany
    {
        return $this->hasMany(ContactUs::class);
    }
    public function placementTestsCreate():HasMany
    {
        return $this->hasMany(PlacementTest::class, 'created_by');
    }

    public function lessonReviews():HasMany
    {
        return $this->hasMany(LessonReview::class , 'assigned_to');
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

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([$this->first_name, $this->last_name]))),
            set: function (?string $value) {
                $value = trim((string) $value);

                if ($value === '') {
                    return [
                        'first_name' => '',
                        'last_name' => '',
                    ];
                }

                $parts = preg_split('/\s+/', $value, 2) ?: [];

                return [
                    'first_name' => $parts[0] ?? '',
                    'last_name' => $parts[1] ?? '',
                ];
            }
        );
    }
}
