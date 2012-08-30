<?php
/**
 * This file is part of the Small Neat Box Framework
 * Copyright (c) 2011-2012 Small Neat Box Ltd.
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace asset\assets;
use asset\filters\FilterInterface;

interface AssetInterface
{
    public function addFilter(FilterInterface $filter);
    public function getFilters();
    public function clearFilters();

    public function getLastModified();

    public function getContent();
    public function getToken();
}
