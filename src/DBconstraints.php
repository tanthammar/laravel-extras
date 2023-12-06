<?php

namespace TantHammar\LaravelExtras;

use Illuminate\Support\Facades\DB;

class DBconstraints
{
    /**
     * DISABLE database foreign key konstraints.<br>
     * Mysql and Postgres compat.
     *
     * @deprecated use Schema::disableForeignKeyConstraints()
     */
    public static function disable(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('SET CONSTRAINTS ALL DEFERRED');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
    }

    /**
     * ENABLE database foreign key konstraints.<br>
     * Mysql and Postgres compat.
     *
     * @deprecated use Schema::enableForeignKeyConstraints()
     */
    public static function enable(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
