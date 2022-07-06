<?php

namespace Xgbnl\Business\Decorates\Contacts;

interface ImageObjectDecorate extends Decorate
{
    /**
     * 组合域名
     * @param mixed $need 文件/图像路径
     * @param string $domain 域名
     * @return mixed
     */
    public function endpoint(mixed $need, string $domain): mixed;

    /**
     * 移除域名
     * @param mixed $need 文件/图像路径
     * @param string $domain 域名
     * @return mixed
     */
    public function removeEndpoint(mixed $need, string $domain): mixed;
}