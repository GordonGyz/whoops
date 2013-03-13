<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;
use Damnit\Exception\FrameIterator;
use Damnit\Exception\ErrorException;
use Exception;

class Inspector
{
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var Damnit\Exception\FrameIterator
     */
    private $framesIterator;

    /**
     * @param Exception $exception The exception to inspect
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getExceptionName()
    {
        return get_class($this->exception);
    }

    /**
     * Returns an iterator for the inspected exception's
     * frames.
     * @return DamnIt\Exception\FrameIterator
     */
    public function getFrames()
    {
        if($this->framesIterator === null) {
            $frames     = $this->exception->getTrace();
            
            // If we're handling an ErrorException thrown by Damnit,
            // get rid of the last, which matches the handleError method,
            // and do not add the current exception to trace
            if($this->exception instanceof ErrorException) {
                array_shift($frames);
            } else {
                $firstFrame = $this->getFrameFromException($this->exception);
                array_unshift($frames, $firstFrame);
            }
            $this->framesIterator = new FrameIterator($frames);
        }

        return $this->framesIterator;
    }

    /**
     * Given an exception, generates an array in the format
     * generated by Exception::getTrace()
     * @param Exception $exception
     * @return array
     */
    protected function getFrameFromException(Exception $exception)
    {
        return array(
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'class' => get_class($exception),
            'args'  => array(
                $exception->getMessage()
            )
        );
    }
}
