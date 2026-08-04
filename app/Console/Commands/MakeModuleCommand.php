<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Scaffolds a simple "lookup" admin CRUD module (name/slug/description/
 * parent/sort_order/status) — migration, model, controller, form requests,
 * and views — from the stubs in stubs/module/. Intended for the ~16
 * straightforward admin modules (Blog Categories, Gallery Categories, FAQ
 * Categories, ...), not for complex modules like Tours or Bookings, which
 * are hand-built.
 *
 * Usage: php artisan module:make GalleryCategory --parent
 */
class MakeModuleCommand extends Command
{
    protected $signature = 'module:make {name}
        {--parent : Add a self-referencing parent_id dropdown}
        {--no-slug : Omit the slug field}
        {--no-description : Omit the description field}
        {--no-status : Omit the status toggle}
        {--no-sort : Omit the sort_order field}';

    protected $description = 'Scaffold a simple admin CRUD module (migration, model, controller, requests, views)';

    protected Filesystem $files;

    public function handle(Filesystem $files): int
    {
        $this->files = $files;

        $class = Str::studly(Str::singular($this->argument('name')));
        $table = Str::snake(Str::pluralStudly($class));
        $variable = Str::camel($class);
        $variablePlural = Str::camel(Str::pluralStudly($class));
        $routeName = Str::kebab(Str::pluralStudly($class));
        $routeParam = Str::snake($class);
        $viewDir = $routeName;
        $title = Str::headline($class);
        $titlePlural = Str::plural($title);

        $hasParent = (bool) $this->option('parent');
        $hasSlug = ! $this->option('no-slug');
        $hasDescription = ! $this->option('no-description');
        $hasStatus = ! $this->option('no-status');
        $hasSort = ! $this->option('no-sort');

        $tokens = compact(
            'class', 'table', 'variable', 'variablePlural', 'routeName',
            'routeParam', 'viewDir', 'title', 'titlePlural'
        );

        $this->makeMigration($tokens, $hasParent, $hasSlug, $hasDescription, $hasStatus, $hasSort);
        $this->makeModel($tokens, $hasParent, $hasSlug, $hasDescription, $hasStatus, $hasSort);
        $this->makeRequests($tokens, $hasParent, $hasSlug, $hasDescription, $hasStatus, $hasSort);
        $this->makeController($tokens, $hasParent, $hasSlug, $hasStatus, $hasSort);
        $this->makeViews($tokens, $hasParent, $hasSlug, $hasDescription, $hasStatus, $hasSort);

        $this->newLine();
        $this->info("Module [{$class}] scaffolded. Next steps:");
        $this->line("  1. Add to routes/admin.php:");
        $this->line("     Route::resource('{$routeName}', \\App\\Http\\Controllers\\Admin\\{$class}Controller::class)->except('show');");
        $this->line('  2. Point a config/admin_nav.php entry at admin.'.$routeName.'.index');
        $this->line('  3. Run migrations: php artisan migrate');

        return self::SUCCESS;
    }

    private function stub(string $name): string
    {
        return $this->files->get(base_path("stubs/module/{$name}.stub"));
    }

    private function render(string $stub, array $tokens): string
    {
        $replace = [];
        foreach ($tokens as $key => $value) {
            $replace['{{ '.$key.' }}'] = $value;
        }

        return strtr($stub, $replace);
    }

    private function writeFile(string $path, string $contents): void
    {
        $this->files->ensureDirectoryExists(dirname($path));

        if ($this->files->exists($path)) {
            $this->warn("Skipped (already exists): {$path}");

            return;
        }

        $this->files->put($path, $contents);
        $this->info("Created: {$path}");
    }

    private function makeMigration(array $t, bool $parent, bool $slug, bool $description, bool $status, bool $sort): void
    {
        $existing = $this->files->glob(database_path("migrations/*_create_{$t['table']}_table.php"));

        if (! empty($existing)) {
            $this->warn("Skipped migration (table [{$t['table']}] already has one): {$existing[0]}");

            return;
        }

        $tokens = $t + [
            'parentColumn' => $parent
                ? "            \$table->foreignId('parent_id')->nullable()->constrained('{$t['table']}')->nullOnDelete();\n"
                : '',
            'slugColumn' => $slug ? "            \$table->string('slug', 150)->unique();\n" : '',
            'descriptionColumn' => $description ? "            \$table->text('description')->nullable();\n" : '',
            'sortColumn' => $sort ? "            \$table->integer('sort_order')->default(0);\n" : '',
            'statusColumn' => $status ? "            \$table->boolean('status')->default(true);\n" : '',
        ];

        $path = database_path('migrations/'.now()->format('Y_m_d_His')."_create_{$t['table']}_table.php");
        $this->writeFile($path, $this->render($this->stub('migration'), $tokens));
    }

    private function makeModel(array $t, bool $parent, bool $slug, bool $description, bool $status, bool $sort): void
    {
        $path = app_path("Models/{$t['class']}.php");

        if ($this->files->exists($path)) {
            $this->warn("Skipped model (already exists): {$path}");

            return;
        }

        $fields = array_filter([
            $parent ? "'parent_id'" : null,
            "'name'",
            $slug ? "'slug'" : null,
            $description ? "'description'" : null,
            $sort ? "'sort_order'" : null,
            $status ? "'status'" : null,
        ]);

        $tokens = $t + [
            'useParent' => $parent
                ? "use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;\nuse Illuminate\\Database\\Eloquent\\Relations\\HasMany;"
                : '',
            'fillable' => implode(', ', $fields),
            'casts' => $status
                ? "\n    protected function casts(): array\n    {\n        return ['status' => 'boolean'];\n    }\n"
                : '',
            'parentRelations' => $parent
                ? "\n    public function parent(): BelongsTo\n    {\n        return \$this->belongsTo(self::class, 'parent_id');\n    }\n\n    public function children(): HasMany\n    {\n        return \$this->hasMany(self::class, 'parent_id');\n    }\n"
                : '',
        ];

        $this->writeFile($path, $this->render($this->stub('model'), $tokens));
    }

    private function makeRequests(array $t, bool $parent, bool $slug, bool $description, bool $status, bool $sort): void
    {
        $rules = array_filter([
            $parent ? "            'parent_id' => ['nullable', 'exists:{$t['table']},id'],\n" : null,
            "            'name' => ['required', 'string', 'max:150'],\n",
            $slug ? "            'slug' => ['nullable', 'string', 'max:150', 'unique:{$t['table']},slug'],\n" : null,
            $description ? "            'description' => ['nullable', 'string'],\n" : null,
            $sort ? "            'sort_order' => ['nullable', 'integer'],\n" : null,
            $status ? "            'status' => ['required', 'boolean'],\n" : null,
        ]);

        $updateRules = array_filter([
            $parent ? "            'parent_id' => ['nullable', 'exists:{$t['table']},id'],\n" : null,
            "            'name' => ['required', 'string', 'max:150'],\n",
            $slug ? "            'slug' => ['nullable', 'string', 'max:150', \Illuminate\Validation\Rule::unique('{$t['table']}', 'slug')->ignore(\$this->route('{$t['routeParam']}'))],\n" : null,
            $description ? "            'description' => ['nullable', 'string'],\n" : null,
            $sort ? "            'sort_order' => ['nullable', 'integer'],\n" : null,
            $status ? "            'status' => ['required', 'boolean'],\n" : null,
        ]);

        $this->writeFile(
            app_path("Http/Requests/Admin/Store{$t['class']}Request.php"),
            $this->render($this->stub('store-request'), $t + ['rules' => rtrim(implode('', $rules))])
        );

        $this->writeFile(
            app_path("Http/Requests/Admin/Update{$t['class']}Request.php"),
            $this->render($this->stub('update-request'), $t + ['updateRules' => rtrim(implode('', $updateRules))])
        );
    }

    private function makeController(array $t, bool $parent, bool $slug, bool $status, bool $sort): void
    {
        $tokens = $t + [
            'indexOrder' => $sort ? "            ->orderBy('sort_order')\n" : "            ->orderBy('name')\n",
            'formOptionsCreate' => $parent
                ? "            'parentOptions' => \\App\\Models\\{$t['class']}::whereNull('parent_id')->orderBy('name')->pluck('name', 'id'),\n"
                : '',
            'formOptionsEdit' => $parent
                ? "            'parentOptions' => \\App\\Models\\{$t['class']}::whereNull('parent_id')->where('id', '!=', \${$t['variable']}->id)->orderBy('name')->pluck('name', 'id'),\n"
                : '',
        ];

        $contents = $this->render($this->stub('controller'), $tokens);

        if ($slug) {
            $contents = str_replace(
                "{$t['class']}::create(\$request->validated());",
                "\$data = \$request->validated();\n        \$data['slug'] = \$data['slug'] ?: \\Illuminate\\Support\\Str::slug(\$data['name']);\n\n        {$t['class']}::create(\$data);",
                $contents
            );
        }

        $this->writeFile(app_path("Http/Controllers/Admin/{$t['class']}Controller.php"), $contents);
    }

    private function makeViews(array $t, bool $parent, bool $slug, bool $description, bool $status, bool $sort): void
    {
        $headers = array_filter([
            "'Name'",
            $parent ? "'Parent'" : null,
            $status ? "'Status'" : null,
            "''",
        ]);

        $formTokens = $t + [
            'parentField' => $parent
                ? "    <x-ui.select label=\"Parent\" name=\"parent_id\" :options=\"\$parentOptions\" :selected=\"\${$t['variable']}->parent_id ?? null\" placeholder=\"None (top-level)\" class=\"sm:col-span-2\" />\n"
                : '',
            'slugField' => $slug
                ? "    <x-ui.input label=\"Slug (optional)\" name=\"slug\" :value=\"\${$t['variable']}->slug ?? ''\" hint=\"Leave blank to auto-generate from name.\" class=\"sm:col-span-2\" />\n"
                : '',
            'descriptionField' => $description
                ? "    <x-ui.textarea label=\"Description\" name=\"description\" :value=\"\${$t['variable']}->description ?? ''\" class=\"sm:col-span-2\" />\n"
                : '',
            'sortField' => $sort
                ? "    <x-ui.input label=\"Sort Order\" name=\"sort_order\" type=\"number\" :value=\"\${$t['variable']}->sort_order ?? 0\" />\n"
                : '',
            'statusField' => $status
                ? "    <div>\n        <input type=\"hidden\" name=\"status\" value=\"0\">\n        <x-ui.checkbox label=\"Active\" name=\"status\" :checked=\"\${$t['variable']}->status ?? true\" />\n    </div>\n"
                : '',
        ];

        $indexTokens = $t + [
            'tableHeaders' => implode(', ', $headers),
            'indexRowParent' => $parent
                ? "                        <td class=\"px-4 py-3 text-gray-500\">{{ \${$t['variable']}->parent?->name ?? '—' }}</td>\n"
                : '',
            'indexRowStatus' => $status
                ? "                        <td class=\"px-4 py-3\"><x-ui.badge :color=\"\${$t['variable']}->status ? 'green' : 'gray'\">{{ \${$t['variable']}->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>\n"
                : '',
        ];

        $this->writeFile(
            resource_path("views/admin/{$t['viewDir']}/form.blade.php"),
            $this->render($this->stub('views/form'), $formTokens)
        );
        $this->writeFile(
            resource_path("views/admin/{$t['viewDir']}/index.blade.php"),
            $this->render($this->stub('views/index'), $indexTokens)
        );
        $this->writeFile(
            resource_path("views/admin/{$t['viewDir']}/create.blade.php"),
            $this->render($this->stub('views/create'), $t)
        );
        $this->writeFile(
            resource_path("views/admin/{$t['viewDir']}/edit.blade.php"),
            $this->render($this->stub('views/edit'), $t)
        );
    }
}
