<!-- Deposit -->
<td>{{ $data['deposit']['a'] }}M</td>
<td class="text-muted">{{ $data['deposit']['t'] }}M</td>
<td class="{{ $data['deposit']['v'] < 0 ? 'text-red' : 'text-green' }}">{{ $data['deposit']['v'] > 0 ? '+' : '' }}{{ $data['deposit']['v'] }}M</td>
<td class="{{ $data['deposit']['g'] < 90 ? 'text-red' : ($data['deposit']['g'] < 100 ? 'text-amber' : 'text-green') }}">{{ $data['deposit']['g'] }}%</td>
<td>
    @if($data['deposit']['trend'] === 'up') <span class="text-green">↑</span>
    @elseif($data['deposit']['trend'] === 'down') <span class="text-red">↓</span>
    @else <span class="text-muted">→</span> @endif
</td>

<!-- Accounts -->
<td>{{ $data['accounts']['a'] }}</td>
<td class="text-muted">{{ $data['accounts']['t'] }}</td>
<td class="{{ $data['accounts']['v'] < 0 ? 'text-red' : 'text-green' }}">{{ $data['accounts']['v'] > 0 ? '+' : '' }}{{ $data['accounts']['v'] }}</td>
<td class="{{ $data['accounts']['g'] < 90 ? 'text-red' : ($data['accounts']['g'] < 100 ? 'text-amber' : 'text-green') }}">{{ $data['accounts']['g'] }}%</td>
<td>
    @if($data['accounts']['trend'] === 'up') <span class="text-green">↑</span>
    @elseif($data['accounts']['trend'] === 'down') <span class="text-red">↓</span>
    @else <span class="text-muted">→</span> @endif
</td>

<!-- Mobile -->
<td>{{ $data['mobile']['a'] }}</td>
<td class="text-muted">{{ $data['mobile']['t'] }}</td>
<td class="{{ $data['mobile']['v'] < 0 ? 'text-red' : 'text-green' }}">{{ $data['mobile']['v'] > 0 ? '+' : '' }}{{ $data['mobile']['v'] }}</td>
<td class="{{ $data['mobile']['g'] < 90 ? 'text-red' : ($data['mobile']['g'] < 100 ? 'text-amber' : 'text-green') }}">{{ $data['mobile']['g'] }}%</td>
<td>
    @if($data['mobile']['trend'] === 'up') <span class="text-green">↑</span>
    @elseif($data['mobile']['trend'] === 'down') <span class="text-red">↓</span>
    @else <span class="text-muted">→</span> @endif
</td>

<!-- ATM -->
<td>{{ number_format($data['atm']['a']) }}</td>
<td class="text-muted">{{ number_format($data['atm']['t']) }}</td>
<td class="{{ $data['atm']['v'] < 0 ? 'text-red' : 'text-green' }}">{{ $data['atm']['v'] > 0 ? '+' : '' }}{{ number_format($data['atm']['v']) }}</td>
<td class="{{ $data['atm']['g'] < 90 ? 'text-red' : ($data['atm']['g'] < 100 ? 'text-amber' : 'text-green') }}">{{ $data['atm']['g'] }}%</td>
<td>
    @if($data['atm']['trend'] === 'up') <span class="text-green">↑</span>
    @elseif($data['atm']['trend'] === 'down') <span class="text-red">↓</span>
    @else <span class="text-muted">→</span> @endif
</td>

<!-- Loans -->
<td>{{ $data['loans']['a'] }}M</td>
<td class="text-muted">{{ $data['loans']['t'] }}M</td>
<td class="{{ $data['loans']['v'] < 0 ? 'text-red' : 'text-green' }}">{{ $data['loans']['v'] > 0 ? '+' : '' }}{{ $data['loans']['v'] }}M</td>
<td class="{{ $data['loans']['g'] < 90 ? 'text-red' : ($data['loans']['g'] < 100 ? 'text-amber' : 'text-green') }}">{{ $data['loans']['g'] }}%</td>
<td>
    @if($data['loans']['trend'] === 'up') <span class="text-green">↑</span>
    @elseif($data['loans']['trend'] === 'down') <span class="text-red">↓</span>
    @else <span class="text-muted">→</span> @endif
</td>

<!-- Overall -->
<td class="font-bold {{ $data['overall']['score'] < 90 ? 'text-red' : ($data['overall']['score'] < 95 ? 'text-amber' : 'text-green') }}">{{ $data['overall']['score'] }}%</td>
<td>
    @if($data['overall']['trend'] === 'up') <span class="text-green">▲</span>
    @elseif($data['overall']['trend'] === 'down') <span class="text-red">▼</span>
    @else <span class="text-muted">●</span> @endif
</td>