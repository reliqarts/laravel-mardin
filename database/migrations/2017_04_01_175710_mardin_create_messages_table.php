<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Cmgmyr\Messenger\Models\Models as MessengerModels;

class MardinCreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $t = MessengerModels::table('messages');

        if (!Schema::hasTable($t)) {
            Schema::create($t, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->text('body');
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
        Schema::dropIfExists(MessengerModels::table('messages'));
    }
}
