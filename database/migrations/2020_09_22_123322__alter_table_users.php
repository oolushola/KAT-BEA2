<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUserProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('date_of_appointment')->nullable()->before('created_at');
            $table->string('current_post_held')->nullable()->after('date_of_appointment');
            $table->string('date_of_birth')->nullable()->after('current_post_held');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('bank_name')->nullable()->after('address');
            $table->string('account_no')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_appointment',
                'current_post_held',
                'date_of_birth',
                'gender',
                'bank_name',
                'account_no'
            ]);
        });
    }
}
