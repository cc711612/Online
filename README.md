<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h1 class="title"><span style="font-family: 'arial black', sans-serif; font-size: 18pt;"><strong>本篇介紹如何在 Laravel 框架中，利用Websocket抓取在線人數。</strong></span></h1>
<p><span style="font-family: 'arial black', sans-serif; font-size: 18pt;"><strong></strong></span></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span class="Comment" style="box-sizing: border-box; -webkit-font-smoothing: antialiased; color: #99968b; font-style: italic;"># </span><span class="Comment" style="color: #99968b; font-family: Menlo, Monaco, monospace; font-size: 14px; box-sizing: border-box; -webkit-font-smoothing: antialiased;">新增Project</span><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;">
laravel new {project}<br /></span></span></span></span></span></span></pre>
<p><strong>修改env</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># .env<br />BROADCAST_DRIVER=pusher
PUSHER_APP_ID=online
PUSHER_APP_KEY=online
PUSHER_APP_SECRET=onlineonline</span></span></span></span></pre>
<p><strong>修改bootstarp.js</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #99968b; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># /resources/js/bootstarp.js
import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    encrypted: false,
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true
});</span></span></pre>
<p><strong>安裝套件</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># cmd(安裝套件)
npm install
composer install
composer require laravel/passport
composer require pusher/pusher-php-server "~3.0"
npm install --save laravel-echo pusher-js <br />composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"<br /></span></span></span></span></span></span></span></span></pre>
<p><strong>新增一個Middleware</strong></p>
<p><strong>因為會使用到Presence頻道，但它是屬於私人頻道，需要被授權才能連上。</strong><strong></strong></p>
<p><strong>於是我們建立一個Guest的Middleware去做認證。</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;">namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthenticateGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $fakeUser = new User();
        $fakeUser-&gt;virtual_client = true;
        $fakeUser-&gt;id = rand(100,1000);
        $fakeUser-&gt;name = Str::random('10');

        $request-&gt;merge(['user' =&gt; $fakeUser]);
        $request-&gt;setUserResolver(function () use ($fakeUser) {
            return $fakeUser;
        });
        return $next($request);
    }
}</span></span></pre>
<p><strong>修改Provider</strong></p>
<p><strong>channel的middleware在這邊設定成剛剛建立的authenticate-guest</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># BroadcastServiceProvider<br />public function boot()
{
    Broadcast::routes(['middleware' =&gt; ['authenticate-guest']]);

    require base_path('routes/channels.php');
}</span></span></span></span></span></pre>
<p><strong>Kernel.php也要記得新增剛剛建立的</strong><strong>authenticate-guest</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># Kernel<br /> protected $routeMiddleware = [
       'auth' =&gt; \App\Http\Middleware\Authenticate::class,
       'auth.basic' =&gt; \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
       'cache.headers' =&gt; \Illuminate\Http\Middleware\SetCacheHeaders::class,
       'can' =&gt; \Illuminate\Auth\Middleware\Authorize::class,
       'guest' =&gt; \App\Http\Middleware\RedirectIfAuthenticated::class,
       'password.confirm' =&gt; \Illuminate\Auth\Middleware\RequirePassword::class,
       'signed' =&gt; \Illuminate\Routing\Middleware\ValidateSignature::class,
       'throttle' =&gt; \Illuminate\Routing\Middleware\ThrottleRequests::class,
       'verified' =&gt; \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
       'authenticate-guest' =&gt; \App\Http\Middleware\AuthenticateGuest::class,
];</span></span></span></span></span></pre>
<p><strong>啟動websocket(預設port:6001)</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># 預設port:6001<br />php artisan websockets:serve<br />//</span></span>php artisan websockets:serve --port=6001</pre>
<p><strong>範例畫面</strong></p>
<p><img src="../../storage/images/2021/10/14/blobid0.png" alt="範例" width="600" height="434" /></p>
<p><b>github範本 :</b></p>
<p><b>&nbsp;<a href="https://github.com/cc711612/Online" title="github" target="_blank" rel="noopener">https://github.com/cc711612/Online</a></b></p>
<p><strong>參考文獻:</strong></p>
<p><b><a href="https://beyondco.de/docs/laravel-websockets/getting-started/introduction" title="參考文獻">https://beyondco.de/docs/laravel-websockets/getting-started/introduction</a><a href="https://beyondco.de/docs/laravel-websockets/getting-started/introduction" target="_blank" rel="noopener"></a></b></p>
<p><a href="https://learnku.com/docs/laravel/8.x/broadcasting/9388#presence-channels" target="_blank" title="參考文獻" rel="noopener"><b>https://learnku.com/docs/laravel/8.x/broadcasting/9388#presence-channels</b></a></p>