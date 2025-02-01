# Rapulo Programming Language

Rapulo adalah bahasa pemrograman sederhana yang dibuat untuk tujuan pembelajaran dan eksplorasi konsep-konsep dasar dalam perancangan bahasa pemrograman. Bahasa ini masih dalam tahap pengembangan dan memiliki fitur-fitur dasar.

## Fitur

*   **Sintaks Sederhana**: Rapulo memiliki sintaks yang mudah dipelajari dan dipahami, mirip dengan bahasa-bahasa scripting populer.
*   **Tipe Data Dinamis**: Tipe data variabel ditentukan secara dinamis saat runtime.
*   **Interpreter**: Rapulo menggunakan interpreter untuk menjalankan kode.
*   **Perintah `echo` dan `print`**: Untuk mencetak string ke layar.

## Cara Menggunakan (Menjalankan `compile.py`)

1.  **Pastikan Python Terinstal**

    Pastikan Anda sudah menginstal Python di sistem Anda. Anda dapat memeriksanya dengan membuka terminal atau *command prompt* dan mengetikkan `python --version` atau `python3 --version`. Jika Python terinstal, Anda akan melihat nomor versinya. Jika belum, Anda perlu mengunduh dan menginstalnya terlebih dahulu dari situs web resmi Python: [https://www.python.org/](https://www.python.org/)

2.  **Navigasi ke Direktori yang Benar**

    Buka terminal atau *command prompt* Anda. Gunakan perintah `cd` untuk berpindah ke direktori tempat Anda menyimpan file `main.py`. Misalnya, jika Anda menyimpan `main.py` di dalam folder bernama `Rapulo`, Anda perlu mengetikkan perintah:

    ```bash
    cd Rapulo
    ```

3.  **Jalankan Perintah**

    Setelah Anda berada di direktori yang benar, ketikkan perintah berikut dan tekan Enter:

    ```bash
    python compile.py <nama_file.rpl>  # Ganti <nama_file.rpl> dengan nama file Rapulo Anda
    ```

    *   `<nama_file.rpl>` adalah argumen yang berisi nama file Rapulo yang ingin Anda jalankan.  Pastikan file ini ada di direktori yang sama dengan `main.py`.

## Contoh

Misalnya, Anda memiliki kode Rapulo di `hello.rpl` seperti ini:

```rapulo
echo "Halo, dunia!"
print 'Ini string dengan kutip tunggal'