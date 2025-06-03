<form action="/test-input-consumption" method="POST">
    @csrf
    <label>Patient ID:</label>
    <input type="number" name="patient_id"><br>

    <label>Tanggal:</label>
    <input type="date" name="tanggal"><br>

    <label>Waktu Makan:</label>
    <select name="waktu_makan">
        <option value="pagi">Pagi</option>
        <option value="siang">Siang</option>
        <option value="malam">Malam</option>
    </select><br>

    <label>Nama Makanan:</label>
    <input type="text" name="nama_makanan"><br>

    <label>Kalori:</label>
    <input type="number" name="kalori"><br>

    <button type="submit">Simpan</button>
</form>
