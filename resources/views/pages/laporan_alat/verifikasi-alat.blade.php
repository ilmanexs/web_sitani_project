
@extends('layouts.layout')
@section('title', 'Laporan Bantuan Alat | Sitani')
@section('content')
    <x-ui.card>
        <div class="mb-5 border-b border-[#E5E8EC]">
            <x-ui.title :title="'Verifikasi Laporan'" />
        </div>
        <x-form.form-alat-verify :laporan="$laporan" />
    </x-ui.card>
@endsection

