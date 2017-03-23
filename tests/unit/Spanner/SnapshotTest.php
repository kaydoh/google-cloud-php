<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Tests\Spanner;

use Google\Cloud\Spanner\Operation;
use Google\Cloud\Spanner\Session\Session;
use Google\Cloud\Spanner\Snapshot;
use Google\Cloud\Spanner\Timestamp;

/**
 * @group spanner
 */
class SnapshotTest extends \PHPUnit_Framework_TestCase
{
    private $timestamp;
    private $snapshot;

    public function setUp()
    {
        $this->timestamp = new Timestamp(new \DateTime);
        $this->snapshot = new Snapshot(
            $this->prophesize(Operation::class)->reveal(),
            $this->prophesize(Session::class)->reveal(),
            'foo',
            $this->timestamp
        );
    }

    public function testReadTimestamp()
    {
        $this->assertEquals($this->timestamp, $this->snapshot->readTimestamp());
    }
}
