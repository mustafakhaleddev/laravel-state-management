# Laravel State Management

A state management solution for Laravel applications inspired by Redux, designed to manage complex application state across services, caching layers, and requests, with support for casting, default state handling, and custom methods.

## Features
- **Shared State Across Application**:Stores are shared globally across the application, making them easily accessible without reinitialization in multiple files.
- **State Persistence and Rehydration**: Manage application state easily, persisting and rehydrating data as needed.
- **Casting Attributes**: Automatically cast attributes to types like collections or custom classes using Laravel's castable functionality.
- **Default State Handling**: Define fallback states to be used when rehydration fails.
- **Custom Store Logic**: Add custom methods to interact with specific store states and manage application logic.

## Installation

Install the package using Composer:

```bash
composer require mkd/laravel-state-management
```

## Basic Usage

### Step 1: Create a Store

You can create a store class using the `store:make` Artisan command:

```bash
php artisan store:make UserStore
```

This command will generate a new store class in your `app/Stores` directory.

### Step 2: Define Your Store

In the generated store, define your attributes and casts. The store will manage the state related to these attributes:

```php
class UserStore extends StoreContract
{
    protected $attributes = [
        'user',
        'email',
        'status'
    ];

    protected $casts = [
        'email' => StringCast::class,  // Custom cast
    ];

    protected $enums = [
        'status' => CustomStatusEnum::class // Use enums for statuses
    ];

    public function default(): array
    {
        return ['user' => User::find($this->key)]; // Fallback if rehydration fails
    }

    public function updateUserName($name)
    {
        $user = $this->getUser();
        $user->name = $name;
        $user->save();
    }
}
```

### Step 3: Using a Store

To interact with a store and manage the state, you can retrieve the store instance and access its methods:

```php
use App\Stores\UserStore;

public function handleState(StateManagement $stateManagement)
{
    // Retrieve the store and set state values
    $userStore = $stateManagement->store(UserStore::class);
    $userStore->setUser(User::first());

    // Call custom methods to manage store data
    $userStore->updateUserName('New Name');
}
```

### Step 4: Handling Persistent State

You can persist and rehydrate states based on a unique key, enabling state restoration between requests:

```php
$userStore = StateManagement::use(UserStore::class);
$userStore->setKey(1);
$userStore->rehydrate();
$status = $userStore->getStatus(); // Retrieve status from the store
```

### Step 5: Defining Custom Casts

If you need custom casting for certain attributes, use the `store-cast:make` command:

```bash
php artisan store-cast:make EmailCast
```

Then, define the casting logic in the generated cast class:

```php
class EmailCast implements StateCastAttribute
{
    public function get($model, string $key, $value, array $attributes)
    {
        return strtolower($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return strtoupper($value);
    }
}
```

### Store Rehydration Example

```php
$settingsStore = StateManagement::use(SettingsStore::class);
$settingsStore->setKey(auth()->user()->id);
$settingsStore->rehydrate();
$countries = $settingsStore->getCountries();
```

## Commands

- `store:make <StoreName>`: Generates a new store class.
- `store-cast:make <CastName>`: Generates a new custom cast class.

## Example Stores

### `SettingsStore`

This store manages application settings and allows for the dynamic update of user preferences.

```php
class SettingsStore extends StoreContract
{
    protected $attributes = ['countries', 'cities', 'user'];
    
    protected $casts = ['countries' => CollectionCast::class, 'cities' => CollectionCast::class];
    
    public function default(): array
    {
        return ['countries' => ['id' => 1, 'name' => 'USA'], 'cities' => ['id' => 2, 'name' => 'New York']];
    }

    public function updateUserSettings($key, $value)
    {
        $this->getUser()->updateSettings($key, $value);
    }
}
```

### `UserNotification`

This store handles sending notifications, such as emails, to users.

```php
class UserNotification extends StoreContract
{
    protected $attributes = ['user', 'email'];
    
    protected $casts = ['email' => EmailCast::class];
    
    public function sendInvoiceEmail(Invoice $invoice)
    {
        $this->getUser()->notify(new InvoiceEmail($invoice));
    }
}
```

 ### `Persist`
By Default Persist is saving the state object in cache so it can be easy rehydrated later with the key
```php
use App\Stores\UserStore;

public function handleState(StateManagement $stateManagement)
{
    $user = User::first();
    // Retrieve the store and set state values
    $userStore = $stateManagement->store(UserStore::class);
    $userStore->setUser($user);
    $userStore->setKey($user->id);

    // Call custom methods to manage store data
    $userStore->updateUserName('New Name');
    $userStore->persist();
}
```
You can override `persist` logic by implementing your own logic in store class
```php
    public function persistUsing()
    {
        //Your own persist logic
        
        //Init custom persist flag
        $this->initCustomPersist()
    }
```

### `rehydrate`
By Default rehydrate is setting the state from cache based on store key
```php
use App\Stores\UserStore;

public function handleState(StateManagement $stateManagement)
{
    $user = User::first();
    // Retrieve the store and set state values
    $userStore = $stateManagement->store(UserStore::class);
    $userStore->setKey($user->id);
    $userStore->rehydrate();
    $userStore->getUser()->name // 'New Name'
}
```
You can override `rehydrate` logic by implementing your own logic in store class
```php
    public function rehydrateUsing()
    {
        // your own rehydrating logic
        
        //Init custom rehydrate flag
        $this->initCustomRehydrate();
    }
```
## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

