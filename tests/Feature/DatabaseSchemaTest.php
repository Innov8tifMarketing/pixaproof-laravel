<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    public function test_the_unused_leads_table_is_dropped(): void
    {
        $this->assertFalse(Schema::hasTable('leads'));
    }

    public function test_statamic_content_and_toolkit_tables_exist(): void
    {
        foreach (['entries', 'collections', 'trees', 'navigations', 'global_sets', 'global_set_variables', 'asset_containers', 'assets_meta', 'addon_settings', 'seo_redirects', 'seo_404s'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table [{$table}] is missing.");
        }
    }
}
