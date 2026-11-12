<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Illuminate\Http\UploadedFile;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

         Storage::extend('azure', function ($app, $config) {

            $connectionString =
                "DefaultEndpointsProtocol=https;" .
                "AccountName={$config['name']};" .
                "AccountKey={$config['key']};" .
                "EndpointSuffix=core.windows.net";

            $client = BlobRestProxy::createBlobService($connectionString);

            $adapter = new AzureBlobStorageAdapter(
                $client,
                $config['container']
            );

            return new Filesystem($adapter);
        });


        Validator::extend('no_jfif', function ($attribute, $value, $parameters, $validator) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return true;
            }
            return strtolower($value->getClientOriginalExtension()) !== 'jfif';
        }, 'The :attribute must not be a JFIF image.');

        Paginator::useBootstrap();
    }
}
