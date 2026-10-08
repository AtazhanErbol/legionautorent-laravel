<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('django_admin_log', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('auth_user')->cascadeOnDelete();
        });
        Schema::table('django_admin_log', function (Blueprint $table): void {
            $table->dropForeign(['content_type_id']);
            $table->foreign('content_type_id')->references('id')->on('django_content_type')->nullOnDelete();
        });
        Schema::table('auth_permission', function (Blueprint $table): void {
            $table->dropForeign(['content_type_id']);
            $table->foreign('content_type_id')->references('id')->on('django_content_type')->cascadeOnDelete();
        });
        Schema::table('auth_group_permissions', function (Blueprint $table): void {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('auth_group')->cascadeOnDelete();
        });
        Schema::table('auth_group_permissions', function (Blueprint $table): void {
            $table->dropForeign(['permission_id']);
            $table->foreign('permission_id')->references('id')->on('auth_permission')->cascadeOnDelete();
        });
        Schema::table('auth_user_groups', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('auth_user')->cascadeOnDelete();
        });
        Schema::table('auth_user_groups', function (Blueprint $table): void {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('auth_group')->cascadeOnDelete();
        });
        Schema::table('auth_user_user_permissions', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('auth_user')->cascadeOnDelete();
        });
        Schema::table('auth_user_user_permissions', function (Blueprint $table): void {
            $table->dropForeign(['permission_id']);
            $table->foreign('permission_id')->references('id')->on('auth_permission')->cascadeOnDelete();
        });
        Schema::table('core_sitesettings', function (Blueprint $table): void {
            $table->dropForeign(['hero_car_id']);
            $table->foreign('hero_car_id')->references('id')->on('cars_car')->nullOnDelete();
        });
        Schema::table('cars_car_cities', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('cars_car_cities', function (Blueprint $table): void {
            $table->dropForeign(['city_id']);
            $table->foreign('city_id')->references('id')->on('locations_city')->cascadeOnDelete();
        });
        Schema::table('cars_car_features', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('cars_car_features', function (Blueprint $table): void {
            $table->dropForeign(['carfeature_id']);
            $table->foreign('carfeature_id')->references('id')->on('cars_carfeature')->cascadeOnDelete();
        });
        Schema::table('cars_car', function (Blueprint $table): void {
            $table->dropForeign(['brand_id']);
            $table->foreign('brand_id')->references('id')->on('cars_carbrand')->restrictOnDelete();
        });
        Schema::table('cars_car', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')->references('id')->on('cars_carcategory')->restrictOnDelete();
        });
        Schema::table('cars_carimage', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('cars_carprice', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('cars_cardiscount', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('cars_carspecification', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('pages_faq', function (Blueprint $table): void {
            $table->dropForeign(['city_id']);
            $table->foreign('city_id')->references('id')->on('locations_city')->cascadeOnDelete();
        });
        Schema::table('pages_faq', function (Blueprint $table): void {
            $table->dropForeign(['page_id']);
            $table->foreign('page_id')->references('id')->on('pages_page')->cascadeOnDelete();
        });
        Schema::table('pages_faq', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->cascadeOnDelete();
        });
        Schema::table('seo_translation', function (Blueprint $table): void {
            $table->dropForeign(['content_type_id']);
            $table->foreign('content_type_id')->references('id')->on('django_content_type')->cascadeOnDelete();
        });
        Schema::table('bookings_bookingrequest', function (Blueprint $table): void {
            $table->dropForeign(['city_id']);
            $table->foreign('city_id')->references('id')->on('locations_city')->restrictOnDelete();
        });
        Schema::table('bookings_bookingrequest', function (Blueprint $table): void {
            $table->dropForeign(['car_id']);
            $table->foreign('car_id')->references('id')->on('cars_car')->restrictOnDelete();
        });
        Schema::table('seo_translation', fn (Blueprint $table) => $table->unique(['content_type_id', 'object_id', 'language'], 'legion_unique_seo_translation'));
        Schema::table('cars_car_cities', fn (Blueprint $table) => $table->unique(['car_id', 'city_id'], 'legion_unique_cars_car_cities'));
        Schema::table('cars_car_features', fn (Blueprint $table) => $table->unique(['car_id', 'carfeature_id'], 'legion_unique_cars_car_features'));
        Schema::table('auth_permission', fn (Blueprint $table) => $table->unique(['content_type_id', 'codename'], 'legion_unique_auth_permission'));
        Schema::table('django_content_type', fn (Blueprint $table) => $table->unique(['app_label', 'model'], 'legion_unique_django_content_type'));
        Schema::table('auth_group_permissions', fn (Blueprint $table) => $table->unique(['group_id', 'permission_id'], 'legion_unique_auth_group_permissions'));
        Schema::table('auth_user_groups', fn (Blueprint $table) => $table->unique(['user_id', 'group_id'], 'legion_unique_auth_user_groups'));
        Schema::table('auth_user_user_permissions', fn (Blueprint $table) => $table->unique(['user_id', 'permission_id'], 'legion_unique_auth_user_user_permissions'));
    }

    public function down(): void
    {
        Schema::table('seo_translation', fn (Blueprint $table) => $table->dropUnique('legion_unique_seo_translation'));
        Schema::table('cars_car_cities', fn (Blueprint $table) => $table->dropUnique('legion_unique_cars_car_cities'));
        Schema::table('cars_car_features', fn (Blueprint $table) => $table->dropUnique('legion_unique_cars_car_features'));
        Schema::table('auth_permission', fn (Blueprint $table) => $table->dropUnique('legion_unique_auth_permission'));
        Schema::table('django_content_type', fn (Blueprint $table) => $table->dropUnique('legion_unique_django_content_type'));
        Schema::table('auth_group_permissions', fn (Blueprint $table) => $table->dropUnique('legion_unique_auth_group_permissions'));
        Schema::table('auth_user_groups', fn (Blueprint $table) => $table->dropUnique('legion_unique_auth_user_groups'));
        Schema::table('auth_user_user_permissions', fn (Blueprint $table) => $table->dropUnique('legion_unique_auth_user_user_permissions'));
    }
};
