@props([
    'variant' => 'default', // 'edit', 'delete', 'view', 'default'
    'icon' => null,
    'label' => null,
])
@php
    $variantConfig = [
        'edit' => ['icon' => 'fas fa-edit', 'label' => 'Edit', 'class' => 'action-btn-edit'],
        'delete' => ['icon' => 'fas fa-trash-alt', 'label' => 'Hapus', 'class' => 'action-btn-delete'],
        'view' => ['icon' => 'fas fa-eye', 'label' => 'Lihat', 'class' => 'action-btn-view'],
        'default' => ['icon' => 'fas fa-cog', 'label' => 'Aksi', 'class' => ''],
    ];

    $config = $variantConfig[$variant] ?? $variantConfig['default'];
    $btnIcon = $icon ?? $config['icon'];
    $btnLabel = $label ?? $config['label'];
    $btnClass = $config['class'];
@endphp

<button {{ $attributes->merge(['class' => "action-btn {$btnClass}"]) }}>
    <i class="{{ $btnIcon }}"></i>
    <span>{{ $btnLabel }}</span>
</button>
