<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachment_tags', function ($table) {
            $table->boolean('is_hidden')->default(false)->after('parent_attachment_tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachment_tags', function ($table) {
            $table->dropColumn('is_hidden');
        });
    }
};
