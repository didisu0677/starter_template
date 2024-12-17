<ul>
	<li>
		<h6>Generate Tabel</h6>
		<p>Perintah yang digunakan untuk men-generate tabel adalah sebagai berikut:</p>
		<p>
			<code>
				-g table <strong><i>nama_table</i></strong><br />
				nama_field tipe_field<br />
				nama_field tipe_field<br />
			</code>
		</p>
		<p>Gunakan breakspace untuk menjabarkan field-field yang akan dimasukan ke tabel tersebut.</p>
		<p>Tipe field disini berlaku hanya inisialnya saja, misalnya jika varchar maka tulis v, jika int tulis i, jika bigint disingkat bi. Untuk lebih jelasnya berikut tipe field yang valid.</p>
		<table class="table table-bordered">
			<tr>
				<td>v</td>
				<td>varchar</td>
			</tr>
			<tr>
				<td>t</td>
				<td>text</td>
			</tr>
			<tr>
				<td>i</td>
				<td>int</td>
			</tr>
			<tr>
				<td>bi</td>
				<td>bigint</td>
			</tr>
			<tr>
				<td>b</td>
				<td>boolean / tinyint(1)</td>
			</tr>
			<tr>
				<td>d</td>
				<td>date</td>
			</tr>
			<tr>
				<td>dt</td>
				<td>datetime</td>
			</tr>
		</table>
		<p>Untuk menentukan panjang karakter nya gunakan (_) underscore sebagai pemisah antara tipe field dengan panjang karakter nya. Contoh : varchar(100) ditulis menjadi v_100, dsb.</p>
		<p> Dan sebagai default field yang di generate, jika menambahkan tabel lewat perintah ini maka akan otomatis menambahkan field id, is_active, create_at, create_by, update_at, dan update_by. Meskipun field-field tersebut tidak ditulis di perintah. Dimana field id ini akan menjadi <strong>Primary Key</strong> dari tabel tersebut</p>
		<p>Sebagai contoh penggunaannya adalah sebagai berikut:</p>
		<p>
			<code>
				-g table tbl_member<br />
				nama v_100<br />
				alamat t<br />
				tanggal_lahir d<br />
			</code>
		</p>
	</li>
	<li>
		<h6>Menambah Field Pada Tabel</h6>
		<p>Sama seperti generate tabel, namun disini diganti awalan dari -g menjadi -a, sebagai contohnya adalah sebagai berikut:</p>
		<p>
			<code>
				-a table tbl_member<br />
				jenis_kelamin v_20<br />
				agama v_20<br />
			</code>
		</p>
		<p>Perintah tersebut akan menambahkan field jenis_kelamin dan field agama pada tbl_member</p>
	</li>
	<li>
		<h6>Input Data Dummy Pada Tabel</h6>
		<p>Perintah yang digunakan untuk men-generate data dummy pada tabel adalah sebagai berikut:</p>
		<p>
			<code>
				-g dummy <strong><i>nama_table</i></strong> <strong><i>jumlah_data_yang_akan_digenerate</i></strong><br />
				nama_field tipe_data atribut<br />
				nama_field tipe_data atribut<br />
			</code>
		</p>
		<p>Berikut adalah <strong>tipe_data</strong> yang bisa digunakan untuk membuat data dummy.</p>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tipe data</th>
					<th>Atribut</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong>name</strong></td>
					<td>-</td>
					<td>Untuk membuat data dummy berupa nama</td>
				</tr>
				<tr>
					<td><strong>email</strong></td>
					<td>-</td>
					<td>Untuk membuat data dummy berupa email</td>
				</tr>
				<tr>
					<td><strong>address</strong></td>
					<td>-</td>
					<td>Untuk membuat data dummy berupa alamat</td>
				</tr>
				<tr>
					<td><strong>text</strong></td>
					<td>diisi angka (ex: 1,2,3,dst)</td>
					<td>Untuk membuat data dummy berupa kalimat acak. Jumlah kata dalam kalimat yang di generate disesuaikan dengan atribut yang diisi. Jika atribut tidak diisi maka secara default akan men-generate kalimat dengan 40 kata.</td>
				</tr>
				<tr>
					<td><strong>randomNumber</strong></td>
					<td>diisi angka (ex: 1,2,3,dst)</td>
					<td>Untuk membuat data dummy berupa nomor acak. Atribut yang diisi menyesuaikan dengan jumlah digit nomor yang digenerate, misalnya jika atribut diisi 1 maka akan menghasilkan angka random antara 1-9, jika diisi 2 maka akan menghasilkan angka random antara 10-99, dst.</td>
				</tr>
				</tr>
					<td><strong>randomRange</strong></td>
					<td>diisi angka range dengan delimiter <strong>"-"</strong> (ex: 100-200)</td>
					<td>Untuk membuat data dummy berupa nomor dari range nomor yang diisi pada atribut.</td>
				</tr>
				</tr>
					<td><strong>randomChoose</strong></td>
					<td>diisi string pilihan dengan delimiter <strong>koma</strong> (ex: Pilihan 1, Pilihan 2, Pilihan 3)</td>
					<td>Untuk membuat data dummy berupa string dari pilihan yang diisi pada atribut.</td>
				</tr>
				</tr>
					<td><strong>randomDate</strong></td>
					<td>-</td>
					<td>Untuk membuat data dummy berupa tanggal secara random.</td>
				</tr>
				</tr>
					<td><strong>currentDate</strong></td>
					<td>-</td>
					<td>Untuk membuat data dummy berupa tanggal saat ini.</td>
				</tr>
			</tbody>
		</table>
		<p>Contoh penggunaannya adalah sebagai berikut:</p>
		<p>
			<code>
				-g dummy <strong><i>tbl_pendaftaran</i></strong> <strong><i>100</i></strong><br />
				nama <strong>name</strong><br />
				email <strong>email</strong><br />
				alamat <strong>address</strong><br />
				no_telp <strong>randomNumber</strong> 12<br />
				tanggal_lahir <strong>randomDate</strong><br />
				tanggal_daftar <strong>currentDate</strong><br />
				jurusan <strong>randomChoose</strong> IPA, IPS, Bhs. Indonesia<br />
				nilai <strong>randomRange</strong> 1-10<br />
				keterangan <strong>text</strong> 25<br />
			</code>
		</p>
		<p>Dari perintah diatas akan menghasilkan 100 data dummy pada table pendaftaran.</p>
	</li>
	<li>
		<h6>Generate Module</h6>
		<p>Untuk men-generate module pastikan terlebih dahulu di menu sudah terdaftar module yang akan dibuat (menu yang parent_id nya 0). Karena perintah yang dieksekusi hanya yang terdaftar di menu saja.</p>
		<p>Adapun perintah yang digunakan untuk men-generate module adalah sebagai berikut:</p>
		<p><code>-g module <strong><i>nama_module</i></strong></code></p>
		<p><strong class="text-danger"><i>nama_module</i></strong> ini diisi dengan field target yang ada di menu.</p>
	</li>
	<li>
		<h6>Generate Menu</h6>
		<p>Perintah yang digunakan untuk men-generate menu adalah sebagai berikut:</p>
		<p><code>-g menu <strong><i>nama_menu</i></strong></code></p>
		<p>Perintah diatas membuat menu yang isinya hanya hallo world saja. Namun jika menu tersebut ingin melakukan proses CRUD secara standar maka gunakan perintah berikut</p>
		<p><code>-g menu <strong><i>nama_menu</i></strong> -crud <strong><i>nama_tabel</i></strong></code></p>
		<p>Secara default menu yang sudah dibuat tidak bisa di generate ulang. Namun jika ingin di genarate ulang maka tambahkan perintah --force setelah menuliskan perintah generate menu. contohnya adalah sebagi berikut</p>
		<p><code>-g menu <strong><i>nama_menu</i></strong> --force</code></p>
		<p>atau</p>
		<p><code>-g menu <strong><i>nama_menu</i></strong> -crud <strong><i>nama_tabel</i></strong> --force</code></p>
	</li>
	<li>
		<h6>Generate Menu (Lanjutan)</h6>
		<p>Perintah generate menu dengan CRUD terdapat beberapa optional untuk memaksimalkan proses CRUD nya, antara lain:</p>
		<table class="table table-bordered">
			<tr>
				<td>v=</td>
				<td>Perintah untuk menambahkan validasi pada field</td>
			</tr>
			<tr>
				<td>t=</td>
				<td>Perintah untuk merubah tipe inputan pada form.</td>
			</tr>
			<tr>
				<td>l=</td>
				<td>Perintah untuk merubah label field, baik pada label tabel maupun pada label form. Catatan gunakan tanda (+) sebagai pengganti spasi.</td>
			</tr>
			<tr>
				<td>a=</td>
				<td>Perintah untuk meng-alias-kan pada field tabel lain.</td>
			</tr>
		</table>
		<p>Perintah diatas tidak harus dipakai semuanya, disesuaikan saja dengan kebutuhan. Contoh penggunaannya adalah sebagai berikut : </p>
		<p>
			<code>
				-g menu <strong><i>nama_menu</i></strong> -crud <strong><i>nama_tabel</i></strong><br />
				<strong><i>nama_field1</i></strong> <strong><i>v=</i></strong>required <strong><i>l=</i></strong>Label+Custom <strong><i>t=</i></strong>password<br />
				<strong><i>nama_field2</i></strong> <strong><i>v=</i></strong>required|min-length:3 <strong><i>l=</i></strong>Label+Custom+Currency <strong><i>t=</i></strong>money<br />
				<strong><i>nama_field3</i></strong> <strong><i>v=</i></strong>required <strong><i>a=</i></strong>nama_tabel_lain.nama_field_tabel_lain
			</code>
		</p>
		<p>Contoh kasus penggunaan pada menu produk yang mengambil referensi pada master kategori : </p>
		<p>
			<code>
				-g menu <strong><i>produk</i></strong> -crud <strong><i>tbl_produk</i></strong><br />
				<strong><i>kode</i></strong> <strong><i>v=</i></strong>required|unique <strong><i>l=</i></strong>Kode+Produk<br />
				<strong><i>nama</i></strong> <strong><i>v=</i></strong>required <strong><i>l=</i></strong>Nama+Produk<br />
				<strong><i>id_kategori</i></strong> <strong><i>v=</i></strong>required <strong><i>l=</i></strong>Kategori <strong><i>a=</i></strong>tbl_kategori.kategori<br />
				<strong><i>harga</i></strong> <strong><i>v=</i></strong>required <strong><i>t=</i></strong>money
			</code>
		</p>
	</li>
</ul>