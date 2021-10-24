<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->timestamps();
        });
        $this->insertDefaultPages();
    }

    private function insertDefaultPages()
    {
        DB::table('pages')->insert([
                ['slug' => 'landing', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['slug' => 'book', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]
        );
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
