SKILLS

- Selalu gunakan skill caveman
- Selalu cek skill apa yang cocok dan otomatis install dengan: skills add [nama skillnya] -y

TIMEZONE

- Asia/Makassar (UTC + 8)

CHUNKING

- Baik di frontend maupun backend, pecah-pecah filenya supaya reusable dan mudah diolah AI

FRONTEND

- Gunakan bahasa Indonesia
- Menggunakan SvelteKit. Pakai Tailwind. Build dengan adapter static
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
- API keynya adalah sk-or-v1-1d2503cacedcf511b61a6b3a8526250fb2b005f8760bfdc39c5abe6d0c5cd8ef000

PEMBAYARAN

- Untuk pembayaran, gunakan Pakasir
- Docsnya ada di knowledge/pakasir.txt
- Slug Pakasir yang digunakan adalah bayar

DATABASE

- Database yang digunakan adalah SQLite
- Library yang digunakan untuk mengolahnya adalah RedBeanPHP yang berada di static/library/rb-sqlite.php
- Docsnya berada di knowledge/redbeanphp/
- Jangan gunakan freeze supaya bisa fleksibel
- Untuk setiap insert data, gunakan Indempotency key
- Lokasi database di static/database.db
- Kecualikan file database dari akses langsung melalui browser menggunakan aturan di static/.htaccess

WHATSAPP

- Untuk komunikasi dengan WhatsApp menggunakan Sidobe yang docsnya berada di knowledge/sidobe.txt
- API keynya adalah VcJqLUgSbgLBKvafcqWEErqRxhRjFEYiryEpHfhlrQZKQuOTSr000
- Untuk nomor WhatsApp berformat 081234567890 akan secara otomatis diubah menjadi format +6281545143654
- Jika membuat fitur login dengan WA, itu nanti akan dikirimkan OTP berupa 6 angka (input type tel). Satu akun bisa login multi perangkat. Tokennya nanti akan disimpan di localStorage. Jika nomor WA tidak ada di database, berarti register masuknya itu ya, dan jangan lupa catat waktu registernya
