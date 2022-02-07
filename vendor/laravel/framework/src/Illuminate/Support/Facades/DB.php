<?php

namespace Illuminate\Support\Facades;

/**
 * @method static \Illuminate\Database\ConnectionInterface connection(string $name = null)
 * @method static \Illuminate\Database\Query\Builder table(string $table, string $as = null)
 * @method static \Illuminate\Database\Query\Expression raw($value)
 * @method static array prepareBindings(array $bindings)
 * @method static array pretend(\Closure $callback)
 * @method static array select(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static bool insert(string $query, array $bindings = [])
 * @method static bool statement(string $query, array $bindings = [])
 * @method static bool unprepared(string $query)
 * @method static int affectingStatement(string $query, array $bindings = [])
 * @method static int delete(string $query, array $bindings = [])
 * @method static int transactionLevel()
 * @method static int update(string $query, array $bindings = [])
 * @method static mixed selectOne(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static mixed transaction(\Closure $callback, int $attempts = 1)
 * @method static string getDefaultConnection()
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static void listen(\Closure $callback)
 * @method static void rollBack(int $toLevel = null)
 * @method static void setDefaultConnection(string $name)
 *
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */

use Illuminate\Support\Str;

trait Filterable
{
    public function scopeFilter($query, $request)
    {
        $params = $request->all();
        foreach ($params as $field => $value) {
            if ($field !==  '_token') {
                $method = 'filter' . Str::studly($field);

                if (!empty($value)) {
                    if (method_exists($this, $method)) {
                        $this->{$method}($query, $value);
                    }
                }
            }
        }

        return $query;
    }
}

class DB extends Facade
{
    use Filterable;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db';
    }

    // Search
    public function filterName($query, $value)
    {
        return $query
            ->where('first_name', 'LIKE', '%' . $value . '%');
    }

    public function filterCareer($query, $value)
    {
        return $query->where('job_title', 'LIKE', '%' . $value . '%');
    }

    public function filterLocation($query, $value)
    {
        return $query->where('address', 'LIKE', '%' . $value . '%');
    }

    public function filterLanguage($query, $value)
    {
        return $query->where('locales', 'LIKE', '%' . $value . '%');
    }

    public function filterGender($query, $value)
    {
        return $query->where('gender', $value);
    }

    public function filterEducation($query, $value)
    {
        return $query->where('school', 'LIKE', '%' . $value . '%');
    }
}
