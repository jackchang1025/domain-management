<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chain_groups', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->unsignedBigInteger('group_id')->comment('所属分组ID');
            $table->string('group_name')->comment('分组名称');
            $table->tinyInteger('chain_type')->default(1)->comment('链接类型，使用ChainType枚举');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chain_groups');
    }
};
