<table>
  <thead>
    <tr>
      <th>CIDADE</th>
      <th>ANO</th>
      @if(!empty($Coordenadores))
      <th>COORDENADORES</th>
      @foreach($Coordenadores->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
      @if(!empty($Implantacao))
      <th>IMPLANTAÇÃO</th>
      @foreach($Implantacao->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
      @if(!empty($Qualidade))
      <th>QUALIDADE</th>
      @foreach($Qualidade->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
      @if(!empty($Analise))
      <th>ANÁLISE</th>
      @foreach($Analise->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
      @if(!empty($Acoes))
      <th>AÇÕES</th>
      @foreach($Acoes->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
      @if(!empty($Monitoramento))
      <th>MONITORAMENTO</th>
      @foreach($Monitoramento->getTableColumns() as $campo)
      <th>{{ $campo }}</th>
      @endforeach
      @endif
    </tr>
  </thead>
  <tbody>

    <tr>
      <TD>{{ $Cidade->municipio.'/'.$Cidade->uf }}</TD>
      <TD>{{ $Ano }}</TD>
      @if(!empty($Coordenadores))
      <TD></TD>
      @foreach($Coordenadores->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
      @if(!empty($Implantacao))
      <TD></TD>
      @foreach($Implantacao->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
      @if(!empty($Qualidade))
      <TD></TD>
      @foreach($Qualidade->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
      @if(!empty($Analise))
      <TD></TD>
      @foreach($Analise->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
      @if(!empty($Acoes))
      <TD></TD>
      @foreach($Acoes->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
      @if(!empty($Monitoramento))
      <TD></TD>
      @foreach($Monitoramento->filterFields() as $campo)
      <TD>{{ $campo }}</TD>
      @endforeach
      @endif
    </tr>

  </tbody>
</table>