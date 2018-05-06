<?php

namespace TiendaNube\Checkout\Http\Router;

class RouterMatch
{
    private $params;

    /**
     * @param string $uri
     * @param string $route
     * @param array $regex
     * @return bool
     */
    public function verify(string $uri, string $route, array $regex = []): bool
    {
        $pattern = $this->mountPattern($route, $regex);
        $result = preg_match($pattern, $uri, $this->params);

        array_shift($this->params);

        return $result;
    }

    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $route
     * @param array $regex
     * @return string
     */
    public function mountPattern(string $route, array $regex = [])
    {
        $parts = explode('/', $route);

        $pattern = [];

        foreach ($parts as $index => $term) {
            if ($index === 0) {
                continue;
            }

            if (preg_match('/{(.*)}/', $term, $matches)) {
                $key = $matches[1];

                $expression = array_key_exists($key, $regex) ?
                    $regex[$key] :
                    '.*';

                $pattern[] = '(' . $expression . ')';
                continue;
            }

            $pattern[] = $term;
        }

        return '/^\/' . implode('\/', $pattern) . '$/';
    }
}