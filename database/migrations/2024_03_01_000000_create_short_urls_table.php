<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('short_url');
            $table->text('long_url');
            $table->unsignedInteger('visit_count')->default(0);
            $table->string('edit_link')->nullable();
            $table->timestamp('last_synced_at')->nullable(); // 最后同步时间
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('short_urls');
    }
}; 