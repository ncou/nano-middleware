<?php

class Middleware// implements RequestHandlerInterface
{
    /**
     * @var array MiddlewareInterface[]|callable[]
     */
    private $middlewares;
    /**
     * @var callable
     */
    private $default;
    /**
     * @var integer
     */
    private $index = 0;
    /**
     * @param array $middlewares
     * @param callable $default
     */
    public function __construct(array $middlewares, callable $default)
    {
        $this->middlewares = $middlewares;
        $this->default = $default;
    }
    /**
     * Process the request using the current middleware.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle($request)
    {
        $middleware = $this->middlewares[$this->index];

        // end of the stack, execute the default callable for creating the response
        if (empty($middleware)) {
            return call_user_func($this->default, $request);
        }

        //if (is_callable($middleware)) {
        if ($middleware instanceof Closure) {
           return $middleware($request, $this->nextHandler());
        } 
        
        return $middleware->process($request, $this->nextHandler());
    }
    /**
     * Get a handler pointing to the next middleware.
     *
     * @return static
     */
    private function nextHandler()
    {
        $copy = clone $this;
        $copy->index++;
        return $copy;
    }

}
