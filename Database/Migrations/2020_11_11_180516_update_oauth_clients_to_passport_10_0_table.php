<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOauthClientsToPassport100Table extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasColumn('oauth_clients', 'secret')) {
      Schema::table('oauth_clients', function (Blueprint $table) {
        $table->string('secret', 100)->nullable()->change();
      });
    }

    if (!Schema::hasColumn('oauth_clients', 'provider')) {
      Schema::table('oauth_clients', function (Blueprint $table) {
        $table->string('provider')->after('secret')->nullable();
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

  }
}
