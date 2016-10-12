<?php namespace React;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class ReactServiceProvider extends ServiceProvider {

  public function boot() {

    $blade = $this->app->make('view')->getEngineResolver()->resolve('blade')->getCompiler();
    $blade->extend(function($view) {
      $pattern = $this->createMatcher('react_component');

      return preg_replace($pattern, '<?php echo app(\'React\')->render$2; ?>', $view);
    });

    $prev = __DIR__ . '/../';

    $this->publishes([
      $prev . 'config/config.php' => $this->app->basePath('config/react.php'),
    ], 'config');
  }

  public function register() {

    $cache = $this->app->make('cache');

    $this->app->bind('React', function() {

      if($this->app->environment('production')
        && $cache->has('reactSource')
        && $cache->has('componentsSource')) {

        $reactSource = $cache->get('reactSource');
        $componentsSource = $cache->get('componentsSource');

      }
      else {

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'react');

        $reactBaseSource = file_get_contents(config('react.source'));
        $reactDomSource = file_get_contents(config('react.dom-source'));
        $reactDomServerSource = file_get_contents(config('react.dom-server-source'));
        $componentsSource = file_get_contents(config('react.components'));
        $reactSource = $reactBaseSource;
        $reactSource .= $reactDomSource;
        $reactSource .= $reactDomServerSource;

        if($this->app->environment('production')) {
          $cache->forever('reactSource', $reactSource);
          $cache->forever('componentsSource', $componentsSource);
        }
      }

      return new React($reactSource, $componentsSource);
    });
  }

  protected function createMatcher($function) {
    return '/(?<!\w)(\s*)@' . $function . '(\s*\(.*\))/';
  }
}
