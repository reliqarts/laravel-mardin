<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Cmgmyr\Messenger\Models\Models as MessengerModels;

class MardinCreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $t = MessengerModels::table('threads');
        
        if (!Schema::hasTable($t)) {
            Schema::create($t, function (Blueprint $table) {
                $table->increments('id');
                $table->string('subject');
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn($t, 'deleted_at')) {
            Schema::table($t, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(MessengerModels::table('threads'));
    }
}
