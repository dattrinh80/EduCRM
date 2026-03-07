<?php

$baseDir = __DIR__ . '/Modules/Marketing/Campaign';
$dirs = [
    '/Domain',
    '/Infrastructure/ReadModels',
    '/Infrastructure/Persistence',
    '/Application/Commands',
    '/Application/Queries',
    '/Presentation/Web/Views',
    '/Presentation/API'
];

foreach ($dirs as $dir) {
    if (!is_dir($baseDir . $dir)) {
        mkdir($baseDir . $dir, 0777, true);
    }
}

// 1. Domain Object
$domain = <<<EOT
<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Domain;

use App\Core\Domain\Entity;

class Campaign extends Entity
{
    public string \$name;
    public ?string \$code;
    public ?string \$channel;
    public ?float \$budget;
    public ?\DateTimeImmutable \$startDate;
    public ?\DateTimeImmutable \$endDate;
    public bool \$isActive;

    public function __construct(
        string \$id,
        string \$name,
        ?string \$code,
        ?string \$channel,
        ?float \$budget,
        ?\DateTimeImmutable \$startDate,
        ?\DateTimeImmutable \$endDate,
        bool \$isActive,
        ?\DateTimeImmutable \$createdAt = null,
        ?\DateTimeImmutable \$updatedAt = null
    ) {
        parent::__construct(\$id, \$createdAt, \$updatedAt);
        \$this->name = \$name;
        \$this->code = \$code;
        \$this->channel = \$channel;
        \$this->budget = \$budget;
        \$this->startDate = \$startDate;
        \$this->endDate = \$endDate;
        \$this->isActive = \$isActive;
    }

    public static function create(
        string \$name,
        ?string \$code,
        ?string \$channel,
        ?float \$budget,
        ?\DateTimeImmutable \$startDate,
        ?\DateTimeImmutable \$endDate
    ): self {
        return new self(
            (string) \Illuminate\Support\Str::uuid(),
            \$name,
            \$code,
            \$channel,
            \$budget,
            \$startDate,
            \$endDate,
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function update(
        string \$name,
        ?string \$code,
        ?string \$channel,
        ?float \$budget,
        ?\DateTimeImmutable \$startDate,
        ?\DateTimeImmutable \$endDate,
        bool \$isActive
    ): void {
        \$this->name = \$name;
        \$this->code = \$code;
        \$this->channel = \$channel;
        \$this->budget = \$budget;
        \$this->startDate = \$startDate;
        \$this->endDate = \$endDate;
        \$this->isActive = \$isActive;
        \$this->updatedAt = new \DateTimeImmutable();
    }
}
EOT;
file_put_contents($baseDir . '/Domain/Campaign.php', $domain);

// 2. Repository Interface
$repoInterface = <<<EOT
<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Domain;

interface CampaignRepositoryInterface
{
    public function save(Campaign \$campaign): void;
    public function findById(string \$id): ?Campaign;
    public function findByCode(string \$code): ?Campaign;
    public function delete(Campaign \$campaign): void;
}
EOT;
file_put_contents($baseDir . '/Domain/CampaignRepositoryInterface.php', $repoInterface);

// 3. Eloquent ReadModel
$readModel = <<<EOT
<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Infrastructure\ReadModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CampaignReadModel extends Model
{
    use HasUuids;

    protected \$table = 'campaigns';
    public \$incrementing = false;
    protected \$keyType = 'string';

    protected \$fillable = [
        'id',
        'name',
        'code',
        'channel',
        'budget',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected \$casts = [
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
EOT;
file_put_contents($baseDir . '/Infrastructure/ReadModels/CampaignReadModel.php', $readModel);

// 4. Eloquent Repository implementation
$repoImpl = <<<EOT
<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign\Infrastructure\Persistence;

use Modules\Marketing\Campaign\Domain\Campaign;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Carbon\Carbon;

class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    public function save(Campaign \$campaign): void
    {
        CampaignReadModel::updateOrCreate(
            ['id' => \$campaign->getId()],
            [
                'name' => \$campaign->name,
                'code' => \$campaign->code,
                'channel' => \$campaign->channel,
                'budget' => \$campaign->budget,
                'start_date' => \$campaign->startDate ? Carbon::instance(\$campaign->startDate) : null,
                'end_date' => \$campaign->endDate ? Carbon::instance(\$campaign->endDate) : null,
                'is_active' => \$campaign->isActive,
                'created_at' => \$campaign->createdAt ? Carbon::instance(\$campaign->createdAt) : now(),
                'updated_at' => \$campaign->updatedAt ? Carbon::instance(\$campaign->updatedAt) : now(),
            ]
        );
    }

    public function findById(string \$id): ?Campaign
    {
        \$model = CampaignReadModel::find(\$id);

        if (!\$model) {
            return null;
        }

        return \$this->toDomain(\$model);
    }

    public function findByCode(string \$code): ?Campaign
    {
        \$model = CampaignReadModel::where('code', \$code)->first();

        if (!\$model) {
            return null;
        }

        return \$this->toDomain(\$model);
    }

    public function delete(Campaign \$campaign): void
    {
        CampaignReadModel::destroy(\$campaign->getId());
    }

    private function toDomain(CampaignReadModel \$model): Campaign
    {
        return new Campaign(
            \$model->id,
            \$model->name,
            \$model->code,
            \$model->channel,
            \$model->budget ? (float)\$model->budget : null,
            \$model->start_date ? new \DateTimeImmutable(\$model->start_date->toDateTimeString()) : null,
            \$model->end_date ? new \DateTimeImmutable(\$model->end_date->toDateTimeString()) : null,
            \$model->is_active,
            \$model->created_at ? new \DateTimeImmutable(\$model->created_at->toDateTimeString()) : null,
            \$model->updated_at ? new \DateTimeImmutable(\$model->updated_at->toDateTimeString()) : null
        );
    }
}
EOT;
file_put_contents($baseDir . '/Infrastructure/Persistence/EloquentCampaignRepository.php', $repoImpl);

// 5. Commands and Handlers
$cmds = [
    'CreateCampaignCommand' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
class CreateCampaignCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $code = null,
        public readonly ?string $channel = null,
        public readonly ?float $budget = null,
        public readonly ?\DateTimeImmutable $startDate = null,
        public readonly ?\DateTimeImmutable $endDate = null
    ) {}
}'
    ],
    'CreateCampaignHandler' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
use Modules\Marketing\Campaign\Domain\Campaign;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
class CreateCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(CreateCampaignCommand $command): string
    {
        if ($command->code && $this->repository->findByCode($command->code)) {
            throw new \Exception("Campaign with this code already exists.");
        }
        $campaign = Campaign::create(
            $command->name,
            $command->code,
            $command->channel,
            $command->budget,
            $command->startDate,
            $command->endDate
        );
        $this->repository->save($campaign);
        return $campaign->getId();
    }
}'
    ],
    'UpdateCampaignCommand' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
class UpdateCampaignCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $code,
        public readonly ?string $channel,
        public readonly ?float $budget,
        public readonly ?\DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate,
        public readonly bool $isActive
    ) {}
}'
    ],
    'UpdateCampaignHandler' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
class UpdateCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(UpdateCampaignCommand $command): void
    {
        $campaign = $this->repository->findById($command->id);
        if (!$campaign) {
            throw new \Exception("Campaign not found.");
        }
        if ($command->code && $command->code !== $campaign->code && $this->repository->findByCode($command->code)) {
            throw new \Exception("Campaign with this code already exists.");
        }
        $campaign->update(
            $command->name,
            $command->code,
            $command->channel,
            $command->budget,
            $command->startDate,
            $command->endDate,
            $command->isActive
        );
        $this->repository->save($campaign);
    }
}'
    ],
    'DeleteCampaignCommand' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
class DeleteCampaignCommand
{
    public function __construct(public readonly string $id) {}
}'
    ],
    'DeleteCampaignHandler' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Commands;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
class DeleteCampaignHandler
{
    public function __construct(private CampaignRepositoryInterface $repository) {}
    public function handle(DeleteCampaignCommand $command): void
    {
        $campaign = $this->repository->findById($command->id);
        if (!$campaign) {
            throw new \Exception("Campaign not found.");
        }
        $this->repository->delete($campaign);
    }
}'
    ],
];
foreach($cmds as $k => $v) {
    file_put_contents($baseDir . "/Application/Commands/$k.php", $v[0]);
}

// 6. Queries
$queries = [
    'GetCampaignsQuery' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Queries;
class GetCampaignsQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?bool $isActive = null
    ) {}
}'
    ],
    'GetCampaignsHandler' => [
        '<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Queries;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Illuminate\Pagination\LengthAwarePaginator;
class GetCampaignsHandler
{
    public function handle(GetCampaignsQuery $query): LengthAwarePaginator
    {
        $dbQuery = CampaignReadModel::query();
        if ($query->search) {
            $dbQuery->where("name", "like", "%" . $query->search . "%")
                     ->orWhere("code", "like", "%" . $query->search . "%");
        }
        if ($query->isActive !== null) {
            $dbQuery->where("is_active", $query->isActive);
        }
        return $dbQuery->orderBy("created_at", "desc")->paginate(15);
    }
}'
    ]
];
foreach($queries as $k => $v) {
    file_put_contents($baseDir . "/Application/Queries/$k.php", $v[0]);
}

// 7. Service Provider
$serviceProvider = <<<EOT
<?php
declare(strict_types=1);

namespace Modules\Marketing\Campaign;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Marketing\Campaign\Domain\CampaignRepositoryInterface;
use Modules\Marketing\Campaign\Infrastructure\Persistence\EloquentCampaignRepository;

class CampaignServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->app->bind(CampaignRepositoryInterface::class, EloquentCampaignRepository::class);
    }

    public function boot(): void
    {
        \$this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        \$this->loadViewsFrom(__DIR__ . '/Presentation/Web/Views', 'campaign');

        Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/campaigns', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.view');
            Route::post('/campaigns', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
            Route::put('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
            Route::delete('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\Web\CampaignWebController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
        });

        Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
            Route::get('/campaigns', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.view');
            Route::post('/campaigns', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
            Route::put('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
            Route::delete('/campaigns/{id}', [\Modules\Marketing\Campaign\Presentation\API\CampaignApiController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
        });
    }
}
EOT;
file_put_contents($baseDir . '/CampaignServiceProvider.php', $serviceProvider);

// 8. Patch Perms Script addition
$permsScript = <<<EOT
<?php
require __DIR__.'/vendor/autoload.php';
\$app = require_once __DIR__.'/bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

\$group = PermissionGroup::firstOrCreate(
    ['name' => 'Campaign Management'],
    ['description' => 'Permissions for managing campaigns']
);

\$perms = [
    'campaigns.view' => 'View Campaigns',
    'campaigns.create' => 'Create Campaigns',
    'campaigns.update' => 'Update Campaigns',
    'campaigns.delete' => 'Delete Campaigns',
];

foreach (\$perms as \$name => \$desc) {
    \$p = Permission::firstOrCreate(['name' => \$name, 'guard_name' => 'web'], ['display_name' => \$desc, 'permission_group_id' => \$group->id]);
}

\$role = Role::where('name', 'Admin')->first();
if (\$role) {
    \$role->givePermissionTo(array_keys(\$perms));
    echo "Permissions mapped to admin. ";
}
echo "Done.";
EOT;
file_put_contents(__DIR__ . '/patch_campaign_perms.php', $permsScript);

echo "Module generated successfully.";
