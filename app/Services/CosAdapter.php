<?php

namespace App\Services;

use Qcloud\Cos\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CosAdapter
{
    protected $client;
    protected $config;
    protected $bucket;

    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
        $this->bucket = $config['bucket'];
    }

    /**
     * 上传文件
     */
    public function put($path, $contents, $options = [])
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
                'Body' => $contents,
                'ContentType' => $this->getContentType($path),
            ]);

            Log::info('COS文件上传成功', [
                'path' => $path,
                'etag' => $result['ETag'] ?? null
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('COS文件上传失败', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 获取文件内容
     */
    public function get($path)
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);

            return $result['Body']->getContents();
        } catch (\Exception $e) {
            Log::error('COS文件获取失败', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 检查文件是否存在
     */
    public function exists($path)
    {
        try {
            $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除文件
     */
    public function delete($path)
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);

            Log::info('COS文件删除成功', ['path' => $path]);
            return true;
        } catch (\Exception $e) {
            Log::error('COS文件删除失败', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 获取文件URL
     */
    public function url($path)
    {
        if (isset($this->config['domain'])) {
            return rtrim($this->config['domain'], '/') . '/' . ltrim($path, '/');
        }
        
        return "https://{$this->bucket}.cos.{$this->config['region']}.myqcloud.com/{$path}";
    }

    /**
     * 获取文件大小
     */
    public function size($path)
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);

            return $result['ContentLength'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 获取文件MIME类型
     */
    public function mimeType($path)
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);

            return $result['ContentType'] ?? 'application/octet-stream';
        } catch (\Exception $e) {
            return 'application/octet-stream';
        }
    }

    /**
     * 获取文件最后修改时间
     */
    public function lastModified($path)
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
            ]);

            return strtotime($result['LastModified']);
        } catch (\Exception $e) {
            return time();
        }
    }

    /**
     * 根据文件扩展名获取Content-Type
     */
    protected function getContentType($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * 分片上传大文件
     */
    public function putLargeFile($path, $filePath, $options = [])
    {
        try {
            $result = $this->client->upload($this->bucket, $path, $filePath, $options);
            
            Log::info('COS大文件上传成功', [
                'path' => $path,
                'file_path' => $filePath,
                'etag' => $result['ETag'] ?? null
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('COS大文件上传失败', [
                'path' => $path,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 