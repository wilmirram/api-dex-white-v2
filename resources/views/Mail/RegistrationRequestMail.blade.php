<h1 style='text-align: center'>Olá, {{$data['name']}}</h1>
<p>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at neque sed lacus pulvinar gravida quis eget lorem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque sed ligula sed nunc ullamcorper facilisis sed et neque. Vivamus bibendum lacus sed erat ultrices, posuere luctus est dignissim. Fusce neque magna, pretium at augue at, dictum commodo nisi. Phasellus pretium sit amet neque id facilisis. Nulla facilisi. Phasellus malesuada, risus non pulvinar commodo, velit turpis dictum felis, ac varius nisi urna posuere nisl. Praesent tempor, neque ac pulvinar facilisis, nisi nulla volutpat ipsum, vel pulvinar nulla leo non sem. Donec dui tellus, auctor sodales pellentesque non, vestibulum sed est. In at tellus sit amet quam varius vestibulum.
</p>
<hr>
<p>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin efficitur, turpis vel gravida lacinia, diam mi congue magna, quis rutrum enim eros id velit. Vivamus sed accumsan sapien. Cras pellentesque felis leo, eu auctor sem fermentum consequat. Donec vel tortor mi. Cras vulputate tempus leo, sed pellentesque quam pharetra sed. Fusce ac pharetra nisi. Ut at facilisis nulla. Nullam euismod dapibus nunc sit amet euismod. Donec vel dignissim leo. Maecenas tempor turpis non lorem rhoncus, vel rutrum orci iaculis. Nullam semper non tellus eget dapibus. Morbi facilisis lorem ut dignissim feugiat. Nunc cursus facilisis urna sit amet ultricies. Donec convallis dui eget elit iaculis, id dapibus est consequat. In hac habitasse platea dictumst.
</p>
<p>
    Vestibulum a ante eget nunc porttitor vehicula. Cras rutrum sem vel risus maximus aliquam. Quisque sed venenatis dui. Nulla tincidunt neque eros, fringilla auctor lacus posuere in. Proin ullamcorper massa sed ultrices faucibus. Quisque tincidunt consequat tincidunt. Sed a libero at erat condimentum lobortis. Vestibulum semper viverra cursus. Pellentesque libero dolor, blandit eget velit eu, molestie mollis sem. Donec vel semper quam, in blandit nibh. Maecenas ullamcorper nibh ultricies risus egestas malesuada. Ut aliquet enim ac commodo congue. Curabitur maximus, ante non fermentum aliquam, massa nulla tempor nibh, quis faucibus dolor nisl et quam. Aenean nec leo fringilla, elementum arcu et, aliquet tellus.
</p>
@if(array_key_exists ( 'password' , $data ))
<p>
    A sua senha é: {{$data['password']}}
</p>
@endif
<hr>
<a style='text-align: center; font-size: 20px; text-decoration: none' href='https://webservice.whiteclub.tech/api/registration-requests/validate/{{$data['token']}}'>Valide seus dados</a>
