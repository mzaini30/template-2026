VARIABLE

$ai = sk-or-v1-adcf6210e14b4f9adbf87f8a27e083992446288af4adf6749c44c66df34cbd97000
$whatsapp = bZYMoXDsIyjUVgognpKrdYylgwKzAOCHgHFjbdLeOJwosdtjAv000
$imagechest = Reo00mku5sftOHJqbZ7L9KqbhFTdjNidvNsM67lw8da4482e000
$pakasir = bayar
$gist = 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000

SKILLS

- Selalu gunakan skill caveman
- Selalu cek skill apa yang cocok dan otomatis install dengan: skills add [nama skillnya] -y

TIMEZONE

- Asia/Makassar (UTC + 8)

CHUNKING

- Baik di frontend maupun backend, pecah-pecah filenya supaya reusable dan mudah diolah AI

UPLOAD FILE

- Untuk upload file, menggunakan Github Gist
- Untuk dokumentasi menggunakan Github Gist bisa dilihat di ./knowledge/gist.txt
- Untuk token Githubnya yaitu $gist
- Setelah berhasil upload, simpan data link yang di files[nama filenya]raw_url di database

FRONTEND

- Gunakan bahasa Indonesia
- Menggunakan SvelteKit
- Tampilan hanya mobile. Ketika dibuka di desktop, max width 600px
- Untuk install dependencies, jangan gunakan npm. Hanya gunakan pnpm
- Setiap alert, ganti dengan toast
- Setiap interaksi, kasih bunyi beep yang cocok, supaya rame aja
- Untuk tampilan, gunakan file DESIGN.md
- Untuk kompresi gambar, menggunakan bantuan i0.wp.com. Untuk docsnya, bisa ditemukan di ./knowledge/i0.txt
- Untuk upload gambar, hanya ke https://imgchest.com/upload. Jadi, hanya ditanya link gambarnya aja dan sertakan link upload gambar tersebut di sekitar input text link gambar (target \_blank)

BACKEND

- Lokasi di folder static. Native PHP aja. Jangan main route pakai route.php. Jadi, betul-betul akses ke direct filenya
- Ketika mode dev, akses dengan http://localhost:8080. Ketika mode production, akses dengan /. Jadi, atur di Node environment
- Tidak boleh membuat file index.php

BUILD

- Setiap halaman, kasih title dan meta social media
- Setelah build, salin build/index.html ke build/index.php dan di situ atur conditioning title dan meta social media (termasuk meta gambar) baik di routing statis maulun dinamis. Ini atur perintahnya di pnpm build
- Di .htaccess disetting supaya semua link itu mengarah ke index.php

AI

- Untuk AI, gunakan OpenRouter
- Untuk docs penggunaannya, cek knowledge/openrouter.txt
- Model yang digunakan adalah openrouter/free
- API keynya adalah $ai
- AI ini digunakan hanya jika aku memintanya. Kalau aku tidak memintanya, jangan pernah sama sekali pasang fitur AI ini

PEMBAYARAN

- Untuk pembayaran, gunakan Pakasir
- Docsnya ada di knowledge/pakasir.txt
- Slug Pakasir yang digunakan adalah $pakasir

DATABASE

- Database yang digunakan adalah SQLite
- Library yang digunakan untuk mengolahnya adalah RedBeanPHP yang berada di static/library/rb-sqlite.php
- Docsnya berada di knowledge/redbeanphp/
- Jangan gunakan freeze supaya bisa fleksibel
- Untuk setiap insert data, gunakan Indempotency key
- Lokasi database di static/database.db
- Kecualikan file database dari akses langsung melalui browser menggunakan aturan di static/.htaccess

ULID

- Untuk di table-table di dalam database, itu selalu ada kolom ulid. Contohnya aja kalau di suatu table perlu kolom nama, alamat, dan nomor_hp, maka isi table tersebut kolom-kolomnya adalah id, ulid, nama, alamat, nomor_hp
- ulid di sini yang akan ditampilkan dalam endpoint API backend. Sehingga, user (browser) tidak mengetahui id sebenarnya dari suatu record karena yang ditampilkan hanya ulid-nya

WHATSAPP

- Untuk komunikasi dengan WhatsApp menggunakan Sidobe yang docsnya berada di knowledge/sidobe.txt
- API keynya adalah $whatsapp
- Untuk nomor WhatsApp berformat 081234567890 akan secara otomatis diubah menjadi format +6281545143654
- Jika membuat fitur login dengan WA, itu nanti akan dikirimkan OTP berupa 6 angka (input type tel). Satu akun bisa login multi perangkat. Tokennya nanti akan disimpan di localStorage. Jika nomor WA tidak ada di database, berarti register masuknya itu ya, dan jangan lupa catat waktu registernya

GAMBAR

- Untuk upload gambar, itu menggunakan ImageChest yang dokumentasinya berada di ./knowledge/imagechest/
- Token API ImageChest milikku adalah : $imagechest
