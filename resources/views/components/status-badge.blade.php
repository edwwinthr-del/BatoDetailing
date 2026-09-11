@props(['status'])

@php
    $colors = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
        'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'cancelled' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'draft' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'issued' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'unpaid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block rounded-full px-2.5 py-0.5 text-xs font-medium '.($colors[$status] ?? 'bg-gray-100 text-gray-700')]) }}>
    {{ __(ucfirst(str_replace('_', ' ', $status))) }}
</span>
