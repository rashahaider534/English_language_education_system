@props(['notification'])

@php
    $notificationRouteResolvers = [
        'level-exception' => fn ($data) => isset($data['level_exception_id'])
            ? route('levelException.show', $data['level_exception_id'])
            : null,
        'delete_lesson' => fn ($data) => isset($data['course_id'])
            ? route('lessons.index', $data['course_id'])
            : null,
        'content_dependency_change' => fn ($data) => isset($data['test_id'])
            ? route('test.show', $data['test_id'])
            : null,
    ];

    $notificationData = is_array($notification->data ?? null) ? $notification->data : [];
    $resolver = $notificationRouteResolvers[$notification->type] ?? null;
    $notificationUrl = null;

    if ($resolver) {
        try {
            $notificationUrl = $resolver($notificationData);
        } catch (\Throwable $e) {
            $notificationUrl = null;
        }
    }
@endphp

@if ($notificationUrl)
    <a href="{{ $notificationUrl }}" {{ $attributes }}>{{ $slot }}</a>
@else
    <div {{ $attributes }}>{{ $slot }}</div>
@endif
