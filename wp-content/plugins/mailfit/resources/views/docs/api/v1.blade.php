@extends('layouts.account')

@section('title', trans('messages.API_Documentation'))

@section('content')
  <div class="api-page">
      <h1>{{ trans('messages.API_Documentation') }}</h1>
        
        <p class="alert alert-info">{!! trans('messages.api_token_guide', ["link" => action("Api\MailListController@index", ["api_token" => "YOUR_API_TOKEN"])]) !!}</p>
      
      <h2>{{ trans('messages.LISTS') }}</h2>
      <table class="table table-box pml-table table-log">
        <tr>
          <th width="1%" class="text-nowrap">{{ trans('messages.HTTP_method') }}</th>
          <th width="40%">{{ trans('messages.Endpoint') }}</th>
          <th>{{ trans('messages.Function') }}</th>
        </tr>
        
        <!-- LISTS -->
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists</a>
          </td>
          <td>
            Get information about all lists
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td>
            <div>
              <div class="description detailed">                
                <h4>Returns</h4>
                <div class="list">
                  List of all user's mail lists in json
                </div>
              </div>
            </div>
          </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{{ action("Api\MailListController@index") }}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{uid}</a>
          </td>
          <td>
            Get information about a specific list
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td>
            <div>
              <div class="description detailed">
                <h4>Returns</h4>
                <div class="list">
                  All list informations in json
                </div>
              </div>
            </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", action("Api\MailListController@show", "-ID-")) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
            
        <!--<tr>
          <td>
            <span class="label label-flat bg-primary">{{ trans('messages.POST') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists</a>
          </td>
          <td>
            Create a new list
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td colspan="2">
            <div>
              <div class="description detailed">
                <h4>Parameters</h4>
                <div class="list">
                    <dl>
                      <dt><var>$name</var> <small class="api-required">required</small></dt>
                      <dd>List's name</dd>
                    </dl>
                    <dl>
                      <dt><var>$from_email</var></dt>
                      <dd>From email</dd>
                    </dl>
                    <dl>
                      <dt><var>$from_name</var></dt>
                      <dd>From name</dd>
                    </dl>
                    <dl>
                      <dt><var>$contact[address_1]</var></dt>
                      <dd>List contact's address 1</dd>
                    </dl>
                    <dl>
                      <dt><var>$contact[address_2]</var></dt>
                      <dd>List contact's address 2</dd>
                    </dl>
                </div>
                    
                <h4>Returns</h4>
                <div class="list">
                  Illuminate\Http\Response
                </div>
              </div>
            </div>
          </td>
        </tr>-->
        
      </table>
        
      <h2>{{ trans('messages.CAMPAIGNS') }}</h2>
      <table class="table table-box pml-table table-log">
        <tr>
          <th width="1%" class="text-nowrap">{{ trans('messages.HTTP_method') }}</th>
          <th width="40%">{{ trans('messages.Endpoint') }}</th>
          <th>{{ trans('messages.Function') }}</th>
        </tr>
        
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/campaigns</a>
          </td>
          <td>
            Get information about all campaigns
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">                
                  <h4>Returns</h4>
                  <div class="list">
                    List of all user's campaigns in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{{ action("Api\CampaignController@index") }}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/campaigns/{uid}</a>
          </td>
          <td>
            Get information about a specific campaign
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Returns</h4>
                  <div class="list">
                    All campaign overview informations in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", action("Api\CampaignController@show", "-ID-")) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
        
      </table>
        
      <h2>{{ trans('messages.SUBSCRIBERS') }}</h2>
      <table class="table table-box pml-table table-log">
        <tr>
          <th width="1%" class="text-nowrap">{{ trans('messages.HTTP_method') }}</th>
          <th width="40%">{{ trans('messages.Endpoint') }}</th>
          <th>{{ trans('messages.Function') }}</th>
        </tr>
        
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers</a>
          </td>
          <td>
            Display all list's subscribers
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Returns</h4>
                  <div class="list">
                    List of all list's subscribers in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{!! str_replace("-LIST_ID-", "<redbold>{list_uid}</redbold>", action("Api\SubscriberController@index", ["list_id" => "-LIST_ID-"])) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
          
        <tr>
          <td>
            <span class="label label-flat bg-info">{{ trans('messages.GET') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers/{uid}</a>
          </td>
          <td>
            Get information about a specific subscriber
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Parameters</h4>
                  <div class="list"><dl>
                    <dt><var>$uid</var></dt>
                    <dd>Subsciber's uid</dd>
                  </dl></div>
          
                  <h4>Returns</h4>
                  <div class="list">
                    All subscriber information in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X GET -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", str_replace("-LIST_ID-", "<redbold>{list_uid}</redbold>", action("Api\SubscriberController@show", ["list_id" => "-LIST_ID-",  "id" => "-ID-"]))) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
          
        <tr>
          <td>
            <span class="label label-flat bg-primary">{{ trans('messages.POST') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers/store</a>
          </td>
          <td>
            Create subscriber for a mail list
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Parameters</h4>
                  <div class="list"><dl>
                    <dt><var>$EMAIL</var></dt></dt>
                    <dd>Subscriber's email</dd>
                    <dt><var>$[OTHER_FIELDS...]</var></dt></dt>
                    <dd>All subscriber's other fields: FIRST_NAME (?), LAST_NAME (?),... (depending on the list fields configuration)</dd>
                  </dl></div>
          
                  <h4>Returns</h4>
                  <div class="list">
                    Creation messages in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X POST -H "accept:application/json" -G \
{!! str_replace("-LIST_ID-", "<redbold>{list_uid}</redbold>", action("Api\SubscriberController@store", ["list_id" => "-LIST_ID-"])) !!}? \
-d api_token={{ Auth::user()->api_token }} \
-d EMAIL=test@gmail.com \
-d FIRST_NAME=Marine \
-d LAST_NAME=Joze
</code></pre>
            </td>
        </tr>
          
          
        <tr>
          <td>
            <span class="label label-flat bg-success">{{ trans('messages.PATCH') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers/{uid}/subscribe</a>
          </td>
          <td>
            Subscribe a subscriber
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Parameters</h4>
                  <div class="list"><dl>
                    <dt><var>$uid</var></dt>
                    <dd>Subsciber's uid</dd>
                  </dl></div>
          
                  <h4>Returns</h4>
                  <div class="list">
                    Result messages in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X PATCH -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", str_replace("-LIST_ID-", "<redbold>{list_uid}</redbold>", action("Api\SubscriberController@subscribe", ["list_id" => "-LIST_ID-",  "id" => "-ID-"]))) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
          
        <tr>
          <td>
            <span class="label label-flat bg-success">{{ trans('messages.PATCH') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers/{uid}/unsubscribe</a>
          </td>
          <td>
            Unsubscribe a subscriber
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
            <td></td>
            <td>
              <div>
                <div class="description detailed">
                  <h4>Parameters</h4>
                  <div class="list"><dl>
                    <dt><var>$uid</var></dt>
                    <dd>Subsciber's uid</dd>
                  </dl></div>
          
                  <h4>Returns</h4>
                  <div class="list">
                    Result messages in json
                  </div>
                </div>
              </div>
            </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X PATCH -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", str_replace("-LIST_ID-", "<redbold>{list_id}</redbold>", action("Api\SubscriberController@unsubscribe", ["list_id" => "-LIST_ID-",  "id" => "-ID-"]))) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
          
          
        <tr>
          <td>
            <span class="label label-flat bg-danger">{{ trans('messages.DELETE') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/lists/{list_uid}/subscribers/{uid}/delete</a>
          </td>
          <td>
            Delete a subscriber
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td>
            <div>
              <div class="description detailed">
                  <h4>Parameters</h4>
                  <div class="list"><dl>
                    <dt><var>$uid</var></dt>
                    <dd>Subsciber's uid</dd>
                  </dl></div>
          
                  <h4>Returns</h4>
                  <div class="list">
                    Result messages in json
                  </div>
              </div>
            </div>
          </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X DELETE -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", str_replace("-LIST_ID-", "<redbold>{list_uid}</redbold>", action("Api\SubscriberController@delete", ["list_id" => "-LIST_ID-",  "id" => "-ID-"]))) !!}? \
-d api_token={{ Auth::user()->api_token }}
</code></pre>
            </td>
        </tr>
        
      </table>
        
      
      <h2>{{ trans('messages.USERS') }}</h2>
      <table class="table table-box pml-table table-log">
        <tr>
          <th width="1%" class="text-nowrap">{{ trans('messages.HTTP_method') }}</th>
          <th width="40%">{{ trans('messages.Endpoint') }}</th>
          <th>{{ trans('messages.Function') }}</th>
        </tr>
        
        <!-- LISTS -->
        <tr>
          <td>
            <span class="label label-flat bg-primary">{{ trans('messages.POST') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/users</a>
          </td>
          <td>
            Create a new user
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td>
            <h4>Parameters</h4>
            <div class="list"><dl>
              <dt><var>$user_group_id</var></dt>
              <dd>User group id</dd>
              <dt><var>$email</var></dt>
              <dd>User's email</dd>
              <dt><var>$first_name</var></dt>
              <dd>User's first name</dd>
              <dt><var>$last_name</var></dt>
              <dd>User's last name</dd>
              <dt><var>$timezone</var></dt>
              <dd>User' timezone</dd>
              <dt><var>$language_id</var></dt>
              <dd>Language id</dd>
              <dt><var>$image &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User's avatar</dd>
            </dl></div>
              
            <div>
              <div class="description detailed">                
                <h4>Returns</h4>
                <div class="list">
                  Result message and user's api token in json
                </div>
              </div>
            </div>
          </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X POST -H "accept:application/json" -G \
{{ action("Api\UserController@store") }}? \
-d api_token={{ Auth::user()->api_token }} \
-d user_group_id=1 \
-d email=user_name@gmail.com \
-d first_name=Luan \
-d last_name=Pham \
-d timezone=America/Godthab \
-d language_id=1 \
-d password=123456
</code></pre>
            </td>
        </tr>
          
        <tr>
          <td>
            <span class="label label-flat bg-success">{{ trans('messages.PATCH') }}</span>
          </td>
          <td>
            <a href="#more" class="toogle-api">/api/v1/users/{uid}</a>
          </td>
          <td>
            Update user information
          </td>
        </tr>
        <tr style="display:none;background: #f6f6f6">
          <td></td>
          <td>
            <h4>Parameters</h4>
            <div class="list"><dl>
              <dt><var>$user_group_id &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User group id</dd>
              <dt><var>$email &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User's email</dd>
              <dt><var>$first_name &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User's first name</dd>
              <dt><var>$last_name &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User's last name</dd>
              <dt><var>$timezone &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User' timezone</dd>
              <dt><var>$language_id &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>Language id</dd>
              <dt><var>$image &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}</span></var></dt>
              <dd>User's avatar</dd>
            </dl></div>
              
            <div>
              <div class="description detailed">                
                <h4>Returns</h4>
                <div class="list">
                  Result message and user's api token in json
                </div>
              </div>
            </div>
          </td>
            <td>
                <h4>Example:</h4>
                <pre class=""><code>curl -X PATCH -H "accept:application/json" -G \
{!! str_replace("-ID-", "<redbold>{uid}</redbold>", action("Api\UserController@update", ['id' => "-ID-"])) !!}? \
-d api_token={{ Auth::user()->api_token }} \
-d user_group_id=1 \
-d email=user_name@gmail.com \
-d first_name=Luan \
-d last_name=Pham \
-d timezone=America/Godthab \
-d language_id=1 \
-d password=123456
</code></pre>
            </td>
        </tr>
      </table>  
      
  </div>
    
    
  <script>
    $(document).ready(function() {
      $(".toogle-api").click(function() {
        $(this).parents("tr").next().toggle();
      });
    });
  </script>
    
@endsection
