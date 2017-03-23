<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Core;

/**
 * Retry implementation.
 *
 * Unlike {@see Google\Cloud\ExponentialBackoff}, Retry requires an implementor
 * to supply wait times for each iteration.
 */
class Retry
{
    /**
     * @var int
     */
    private $retries;

    /**
     * @var callable
     */
    private $retryFunction;

    /**
     * @var callable
     */
    private $delayFunction;

    /**
     * @param int $retries Maximum number of retries for a failed request.
     * @param callable $delayFunction A function returning an array of format
     *        `['seconds' => (int >= 0), 'nanos' => (int >= 0)] specifying how
     *        long an operation should pause before retrying. Should accept a
     *        single argument of type `\Exception`.
     * @param callable $retryFunction [optional] returns bool for whether or not
     *        to retry.
     */
    public function __construct(
        $retries,
        callable $delayFunction,
        callable $retryFunction = null
    ) {
        $this->retries = $retries !== null ? (int) $retries : 3;
        $this->retryFunction = $retryFunction;
        $this->delayFunction = $delayFunction;
    }

    /**
     * Executes the retry process.
     *
     * @param callable $function
     * @param array $arguments [optional]
     * @return mixed
     * @throws \Exception The last exception caught while retrying.
     */
    public function execute(callable $function, array $arguments = [])
    {
        $delayFunction = $this->delayFunction;
        $retryAttempt = 0;
        $exception = null;

        while (true) {
            try {
                return call_user_func_array($function, $arguments);
            } catch (\Exception $exception) {
                if ($this->retryFunction) {
                    if (!call_user_func($this->retryFunction, $exception)) {
                        throw $exception;
                    }
                }

                if ($retryAttempt >= $this->retries) {
                    break;
                }

                $delayFunction($exception);
                $retryAttempt++;
            }
        }

        throw $exception;
    }

    /**
     * @param callable $delayFunction
     * @return void
     */
    public function setDelayFunction(callable $delayFunction)
    {
        $this->delayFunction = $delayFunction;
    }
}
