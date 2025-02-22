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
        Schema::create('chains', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->string('chain_title')->nullable()->comment('链接标题');
            $table->unsignedBigInteger('domain')->comment('域名ID');
            $table->text('target_url')->comment('目标网址');
            $table->string('status')->nullable()->comment('状态');
            $table->timestamp('create_time')->nullable()->comment('创建时间');
            $table->unsignedInteger('pv_history')->default(0)->comment('历史访问量');
            $table->unsignedInteger('pv_today')->default(0)->comment('今日访问量');
            $table->string('chain')->unique()->comment('链接后缀');
            $table->string('domain_url')->comment('域名地址');
            $table->tinyInteger('domain_status')->default(1)->comment('域名状态(1:已生效 99:未生效)');
            $table->tinyInteger('type')->comment('链接类型');
            $table->tinyInteger('sub_type')->comment('链接子类型');
            $table->string('render_url')->comment('渲染网址');
            $table->timestamps();

            $table->unsignedBigInteger('group_id')->nullable()->comment('所属分组ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chains');
    }
};
