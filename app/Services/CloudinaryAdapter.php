<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Cloudinary;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;

class CloudinaryAdapter implements FilesystemAdapter
{
    protected Cloudinary $cloudinary;
    protected string $folder;

    public function __construct(array $config)
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $config['cloud_name'],
                'api_key'    => $config['api_key'],
                'api_secret' => $config['api_secret'],
            ],
            'url' => ['secure' => true],
        ]);

        $this->folder = rtrim($config['folder'] ?? '', '/');
    }

    public function getUrl(string $path): string
    {
        $publicId = $this->prefixPath($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $cloudName = $this->cloudinary->configuration->cloud->cloudName;

        return "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}.{$ext}";
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'cld');
        file_put_contents($tmpFile, $contents);

        try {
            (new UploadApi($this->cloudinary))->upload($tmpFile, [
                'public_id'       => $this->prefixPath(pathinfo($path, PATHINFO_FILENAME)),
                'folder'          => $this->folder . '/' . pathinfo($path, PATHINFO_DIRNAME),
                'resource_type'   => 'image',
                'overwrite'       => true,
                'invalidate'      => true,
            ]);
        } catch (\Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        } finally {
            @unlink($tmpFile);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'cld');
        file_put_contents($tmpFile, stream_get_contents($contents));

        try {
            $publicId = pathinfo($path, PATHINFO_FILENAME);
            $dir = pathinfo($path, PATHINFO_DIRNAME);
            $folder = $this->folder;
            if ($dir && $dir !== '.') {
                $folder .= '/' . $dir;
            }

            (new UploadApi($this->cloudinary))->upload($tmpFile, [
                'public_id'       => $publicId,
                'folder'          => $folder,
                'resource_type'   => 'image',
                'overwrite'       => true,
                'invalidate'      => true,
            ]);
        } catch (\Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        } finally {
            @unlink($tmpFile);
        }
    }

    public function read(string $path): string
    {
        try {
            return file_get_contents($this->getUrl($path));
        } catch (\Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function readStream(string $path)
    {
        try {
            return fopen($this->getUrl($path), 'rb');
        } catch (\Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function delete(string $path): void
    {
        try {
            (new UploadApi($this->cloudinary))->destroy($this->prefixPath(pathinfo($path, PATHINFO_FILENAME)));
        } catch (\Throwable $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function deleteDirectory(string $path): void
    {
        try {
            (new AdminApi($this->cloudinary))->deleteFolder($this->prefixPath($path));
        } catch (\Throwable) {}
    }

    public function createDirectory(string $path, Config $config): void {}

    public function fileExists(string $path): bool
    {
        try {
            (new AdminApi($this->cloudinary))->asset($this->prefixPath(pathinfo($path, PATHINFO_FILENAME)));
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        return true;
    }

    public function setVisibility(string $path, string $visibility): void {}

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, null, 'image/' . pathinfo($path, PATHINFO_EXTENSION));
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $contents = $this->read($source);
        $this->write($destination, $contents, $config);
    }

    protected function prefixPath(string $path): string
    {
        $path = ltrim($path, '/');
        if ($path === '.' || $path === '') return $this->folder;
        return $this->folder ? $this->folder . '/' . $path : $path;
    }
}
