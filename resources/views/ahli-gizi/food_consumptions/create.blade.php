@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Input Konsumsi Makanan</h2>

    <form action="{{ route('ahli-gizi.food-consumptions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="patient_id" class="form-label">Pasien</label>
            <select name="patient_id" id="patient_id" class="form-select" required>
                <option value="">-- Pilih Pasien --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="menu_id" class="form-label">Menu Makanan</label>
            <select name="menu_id" id="menu_id" class="form-select" required>
                <option value="">-- Pilih Menu --</option>
                @foreach ($menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->nama_makanan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="waktu_makan" class="form-label">Waktu Makan</label>
            <select name="waktu_makan" id="waktu_makan" class="form-select" required>
                <option value="">-- Pilih Waktu --</option>
                <option value="pagi">Pagi</option>
                <option value="siang">Siang</option>
                <option value="malam">Malam</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Konsumsi</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
