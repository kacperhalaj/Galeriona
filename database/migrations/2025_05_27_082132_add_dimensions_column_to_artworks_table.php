<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (!Schema::hasColumn('artworks', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('artworks', 'width')) {
                $table->float('width')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('artworks', 'height')) {
                $table->float('height')->nullable()->after('width');
            }
            if (!Schema::hasColumn('artworks', 'depth')) {
                $table->float('depth')->nullable()->after('height');
            }
        });
    }

    public function down()
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'width', 'height', 'depth']);
        });
    }
};
