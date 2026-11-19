<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const SOURCE_DATE = '2026-03-31';

    public function up(): void
    {
        $schema = Schema::connection('core');
        if (! $schema->hasTable('banks')) {
            $schema->create('banks', function (Blueprint $table): void {
                $table->string('id', 48)->primary();
                $table->string('bank_code', 3)->index();
                $table->string('bic', 11)->nullable()->index();
                $table->string('name', 180);
                $table->string('short_name', 100)->nullable();
                $table->string('status', 16)->default('active')->index();
                $table->date('source_updated_at')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();
        $banks = [];
        foreach ($this->bankData() as $index => [$bic, $name, $shortName, $code]) {
            $id = 'bank-'.$code.'-'.Str::slug($bic);
            $banks[] = [
                'id' => $id,
                'bank_code' => $code,
                'bic' => $bic,
                'name' => $name,
                'short_name' => $shortName,
                'status' => 'active',
                'source_updated_at' => self::SOURCE_DATE,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($banks, 50) as $chunk) {
            DB::connection('core')->table('banks')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        // Preserve master data if the migration is rolled back after use.
    }

    /** @return list<array{string, string, string, string}> */
    private function bankData(): array
    {
        return [
            ['ABALIDBS', 'PT Bank Pembangunan Daerah Bali', 'BPD Bali', '129'],
            ['AGTBIDJA', 'PT Bank Raya Indonesia Tbk', 'Bank Raya Indonesia', '494'],
            ['ALOBIDJA', 'PT Allo Bank Indonesia Tbk', 'Allo Bank Indonesia', '567'],
            ['ANZBIDJX', 'PT Bank ANZ Indonesia', 'ANZ Indonesia', '061'],
            ['ARFAIDJ1', 'PT Bank Panin Dubai Syariah', 'Bank Panin Dubai Syariah', '517'],
            ['ARTGIDJA', 'PT Bank Artha Graha Internasional Tbk', 'BAG Internasional', '037'],
            ['AWANIDJA', 'PT Bank QNB Indonesia Tbk', 'Bank QNB', '167'],
            ['BBAIIDJA', 'PT Bank Bumi Arta', 'BBA', '076'],
            ['BBBAIDJA', 'PT Bank Permata Tbk', 'Bank Permata', '013'],
            ['BBIJIDJA', 'PT Bank UOB Indonesia', 'UOB Indonesia', '023'],
            ['BBLUIDJA', 'PT Bank Digital BCA', 'BCA Digital', '501'],
            ['BBUKIDJA', 'PT Bank KB Indonesia Tbk', 'KB Indonesia', '441'],
            ['BCIAIDJA', 'PT Bank Capital Indonesia', 'Bank Capital', '054'],
            ['BDINIDJA', 'PT Bank Danamon Indonesia Tbk', 'Bank Danamon', '011'],
            ['BDKIIDJ1', 'PT Bank DKI', 'Bank DKI', '111'],
            ['BIDXIDJA', 'Bank Index Selindo', 'Bank Index', '555'],
            ['BKCHIDJA', 'Bank of China (Hong Kong) Limited', 'BOC Limited', '069'],
            ['BKIDIDJA', 'PT Bank of India Indonesia Tbk', 'Bank of India Indonesia', '146'],
            ['BMRIIDJA', 'PT Bank Mandiri (Persero) Tbk', 'Bank Mandiri', '008'],
            ['BMSEIDJA', 'PT Bank Multiarta Sentosa Tbk', 'PT Bank MAS', '548'],
            ['BNIAIDJA', 'PT Bank CIMB Niaga Tbk', 'Bank CIMB', '022'],
            ['BNINIDJA', 'PT Bank Negara Indonesia (Persero)', 'Bank BNI', '009'],
            ['BNPAIDJA', 'PT Bank BNP Paribas Indonesia', 'BNP Paribas', '057'],
            ['BOFAID2X', 'Bank of America N.A.', 'BOA', '033'],
            ['BOTKIDJX', 'MUFG Bank, Ltd.', 'MUFG', '042'],
            ['BPIAIDJA', 'PT Bank Resona Perdania', 'Bank Resona', '047'],
            ['BRINIDJA', 'PT Bank Rakyat Indonesia (Persero)', 'BRI', '002'],
            ['BSDRIDJA', 'PT Bank Woori Saudara Indonesia 1906 Tbk', 'Bank Woori Saudara', '212'],
            ['BSMDIDJA', 'PT Bank Syariah Indonesia Tbk', 'BSI', '451'],
            ['BSSPIDSP', 'PT BPD Sumsel dan Babel', 'BPD Sumsel Babel', '120'],
            ['BSYAIDJA', 'PT Bank BCA Syariah', 'Bank BCA Syariah', '536'],
            ['BTANIDJA', 'PT Bank Tabungan Negara (Persero)', 'BTN', '200'],
            ['BUMIIDJA', 'PT Bank MNC Internasional Tbk', 'MNC Bank', '485'],
            ['BUSTIDJ1', 'PT Krom Bank Indonesia Tbk', 'Krom Bank Indonesia', '459'],
            ['BUTGIDJ1', 'PT Bank Mega Syariah', 'Bank Mega Syariah', '506'],
            ['CENAIDJA', 'PT Bank Central Asia Tbk', 'BCA', '014'],
            ['CHASIDJX', 'Kantor Cabang JPMorgan Chase Bank N.A.', 'JPMorgan Bank', '032'],
            ['CICTIDJA', 'PT Bank JTrust Indonesia Tbk', 'Bank JTrust', '095'],
            ['CITIIDJX', 'Citibank N.A.', 'Citibank', '031'],
            ['CTCBIDJA', 'PT Bank CTBC Indonesia', 'CTBC Indonesia', '949'],
            ['DBSBIDJA', 'PT Bank DBS Indonesia', 'DBS', '046'],
            ['DEUTIDJA', 'Deutsche Bank AG', 'Deutsche Bank', '067'],
            ['FAMAIDJ1', 'PT Super Bank Indonesia', 'Super Bank', '562'],
            ['GNESIDJA', 'PT Bank Ganesha', 'Bank Ganesha', '161'],
            ['HBNIIDJA', 'PT Bank Hibank Indonesia', 'Hibank', '553'],
            ['HNBNIDJA', 'PT Bank KEB Hana Indonesia', 'Bank KEB Hana', '484'],
            ['HSBCIDJA', 'PT Bank HSBC Indonesia', 'Bank HSBC Indonesia', '087'],
            ['IAPTIDJA', 'PT Bank Ina Perdana Tbk', 'Bank Ina', '513'],
            ['IBBKIDJA', 'PT Bank Maybank Indonesia Tbk', 'Bank Maybank', '016'],
            ['IBKOIDJA', 'PT Bank IBK Indonesia Tbk', 'Bank IBK', '945'],
            ['ICBKIDJA', 'PT Bank ICBC Indonesia', 'Bank ICBC', '164'],
            ['JAGBIDJA', 'PT Bank Jago Tbk', 'Bank Jago', '542'],
            ['JSABIDJ1', 'PT Bank Saqu Indonesia', 'Bank Saqu', '472'],
            ['LFIBIDJ1', 'PT Bank Nationalnobu', 'Bank Nationalnobu', '503'],
            ['LMANIDJ1', 'PT Bank Oke Indonesia Tbk', 'Bank Oke', '526'],
            ['LOMAIDJ1', 'PT Bank Amar Indonesia', 'Bank Amar', '531'],
            ['MASDIDJ1', 'PT Bank Maspion', 'Bank Maspion', '157'],
            ['MAYAIDJA', 'PT Bank Mayapada Tbk', 'Bank Mayapada', '097'],
            ['MCORIDJA', 'PT Bank China Construction Bank Indonesia Tbk', 'China Construction Bank', '036'],
            ['MEDHIDS1', 'PT Bank Mestika Dharma', 'Bank Mestika', '151'],
            ['MEEKIDJ1', 'PT Bank Shinhan Indonesia', 'Bank Shinhan Indonesia', '152'],
            ['MEGAIDJA', 'PT Bank Mega Tbk', 'Bank Mega', '426'],
            ['MHCCIDJA', 'PT Bank Mizuho Indonesia', 'Bank Mizuho', '048'],
            ['MUABIDJA', 'PT Bank Muamalat Indonesia', 'Bank Muamalat', '147'],
            ['NANOIDJ1', 'PT Bank Nano Syariah', 'Nano Bank', '253'],
            ['NETBIDJA', 'PT Bank Aladin Syariah Tbk', 'Bank Aladin Syariah', '947'],
            ['NISPIDJA', 'PT Bank OCBC NISP Tbk', 'Bank OCBC NISP', '028'],
            ['PDBBIDJ1', 'PT Bank Pembangunan Daerah Banten', 'BPD Banten', '137'],
            ['PDBKIDJ1', 'PT BPD Bengkulu', 'Bank Bengkulu', '133'],
            ['PDIJIDJ1', 'PT Bank Pembangunan Daerah Papua', 'BPD Papua', '132'],
            ['PDJBIDJA', 'PT Bank Pembangunan Daerah Jawa Barat dan Banten', 'Bank BJB', '110'],
            ['PDJGIDJ1', 'PT Bank Pembangunan Daerah Jawa Tengah', 'Bank Jateng', '113'],
            ['PDJMIDJ1', 'PT Bank Pembangunan Daerah Jambi', 'BPD Jambi', '115'],
            ['PDJTIDJ1', 'PT Bank Pembangunan Daerah Jawa Timur', 'Bank Jatim', '114'],
            ['PDKBIDJ1', 'PT Bank Pembangunan Daerah Kalimantan Barat', 'BPD Kalbar', '123'],
            ['PDKGIDJ1', 'PT BPD Kalimantan Tengah', 'BPD Kalteng', '125'],
            ['PDKSIDJ1', 'PT Bank Pembangunan Daerah Kalimantan Selatan', 'BPD Kalsel', '122'],
            ['PDKTIDJ1', 'PT Bank Pembangunan Daerah Kaltim dan Kaltara', 'BPD Kaltimtara', '124'],
            ['PDLPIDJ1', 'PT Bank Pembangunan Daerah Lampung', 'Bank Lampung', '121'],
            ['PDMLIDJ1', 'PT Bank Pembangunan Daerah Maluku dan Maluku Utara', 'Bank Maluku Malut', '131'],
            ['PDNBIDJ1', 'PT Bank Pembangunan Daerah Nusa Tenggara Barat Syariah', 'Bank NTB Syariah', '128'],
            ['PDNTIDJA', 'PT Bank Pembangunan Daerah Nusa Tenggara Timur', 'BPD NTT', '130'],
            ['PDRIIDJA', 'PT Bank Pembangunan Daerah Riau Kepri Syariah', 'Bank Riau Kepri Syariah', '119'],
            ['PDSBIDJ1', 'PT Bank Nagari', 'Bank Nagari', '118'],
            ['PDSUIDJ1', 'PT Bank Pembangunan Daerah Sumatera Utara', 'Bank Sumut', '117'],
            ['PDWGIDJ1', 'PT BPD Sulawesi Tengah', 'BPD Sulteng', '134'],
            ['PDWRIDJ1', 'PT BPD Sulawesi Tenggara', 'BPD Sultra', '135'],
            ['PDWSIDJA', 'PT Bank Sulselbar', 'Bank Sulselbar', '126'],
            ['PDWUIDJ1', 'PT BPD Sulawesi Utara Gorontalo', 'BPD Sulut', '127'],
            ['PDYKIDJ1', 'PT Bank Pembangunan Daerah Daerah Istimewa Yogyakarta', 'Bank BPD DIY', '112'],
            ['PINBIDJA', 'PT Panin Bank Tbk', 'Panin Bank', '019'],
            ['PUBAIDJ1', 'PT Bank BTPN Syariah Tbk', 'Bank BTPN Syariah', '547'],
            ['SAHMIDJA', 'PT Bank Sahabat Sampoerna', 'Bank Sampoerna', '523'],
            ['SBIDIDJA', 'PT Bank SBI Indonesia', 'Bank SBI', '498'],
            ['SBJKIDJA', 'PT Bank Sinarmas', 'Bank Sinarmas', '153'],
            ['SCBLIDJX', 'Standard Chartered Bank', 'Standard Chartered', '050'],
            ['SDOBIDJ1', 'PT Bank KB Bukopin Syariah', 'KB Bukopin Syariah', '521'],
            ['SIHBIDJ1', 'PT Bank Mandiri Taspen', 'Bank Mandiri Taspen', '564'],
            ['SSPIIDJA', 'PT Bank SeaBank Indonesia', 'SeaBank', '535'],
            ['SUNIIDJA', 'PT Bank SMBC Indonesia Tbk', 'Bank SMBC', '213'],
            ['SWAGIDJ1', 'PT Bank Syariah Nasional', 'BSN', '405'],
            ['SYACIDJ1', 'PT Bank Aceh Syariah', 'Bank Aceh', '116'],
            ['SYATIDJ1', 'PT Bank Jago Tbk Unit Usaha Syariah', 'Bank Jago UUS', '542'],
            ['SYBBIDJ1', 'PT Bank Permata Tbk Unit Usaha Syariah', 'Bank Permata UUS', '013'],
            ['SYBDIDJ1', 'PT Bank Danamon Indonesia Unit Usaha Syariah', 'Bank Danamon UUS', '011'],
            ['SYBKIDJ1', 'PT Bank Maybank Indonesia Tbk Unit Usaha Syariah', 'Bank Maybank UUS', '016'],
            ['SYDKIDJ1', 'PT Bank DKI Unit Usaha Syariah', 'Bank DKI UUS', '111'],
            ['SYJBIDJ1', 'PT Bank Jabar Banten Syariah', 'Bank Jabar Syariah', '425'],
            ['SYJGIDJ1', 'PT Bank Pembangunan Daerah Jawa Tengah Unit Usaha Syariah', 'Bank Jateng UUS', '113'],
            ['SYJMIDJ1', 'PT Bank Pembangunan Daerah Jambi Unit Usaha Syariah', 'BPD Jambi UUS', '115'],
            ['SYJTIDJ1', 'Bank Jatim Unit Usaha Syariah', 'Bank Jatim UUS', '114'],
            ['SYKBIDJ1', 'PT Bank Pembangunan Daerah Kalimantan Barat Unit Usaha Syariah', 'BPD Kalbar UUS', '123'],
            ['SYKSIDJ1', 'PT Bank Pembangunan Daerah Kalimantan Selatan Unit Usaha Syariah', 'BPD Kalsel UUS', '122'],
            ['SYKTIDJ1', 'PT Bank Pembangunan Daerah Kaltim dan Kaltara Unit Usaha Syariah', 'BPD Kaltimtara UUS', '124'],
            ['SYNAIDJ1', 'PT Bank CIMB Niaga Tbk Unit Usaha Syariah', 'Bank CIMB Niaga UUS', '022'],
            ['SYONIDJ1', 'PT Bank OCBC NISP Tbk Unit Usaha Syariah', 'Bank OCBC NISP UUS', '028'],
            ['SYSBIDJ1', 'PT Bank Nagari Unit Usaha Syariah', 'Bank Nagari UUS', '118'],
            ['SYSSIDJ1', 'PT BPD Sumsel dan Babel Unit Usaha Syariah', 'BPD Sumsel Babel UUS', '120'],
            ['SYSUIDJ1', 'PT Bank Pembangunan Daerah Sumatera Utara Unit Usaha Syariah', 'BPD Sumut UUS', '117'],
            ['SYWSIDJ1', 'PT Bank Sulselbar Unit Usaha Syariah', 'Bank Sulselbar UUS', '126'],
            ['SYYKIDJ1', 'PT Bank Pembangunan Daerah Daerah Istimewa Yogyakarta Unit Usaha Syariah', 'Bank BPD DIY UUS', '112'],
            ['VICTIDJ1', 'PT Bank Victoria International', 'Bank Victoria', '566'],
            ['YUDBIDJ1', 'PT Bank Neo Commerce Tbk', 'Bank Neo Commerce', '490'],
        ];
    }
};
