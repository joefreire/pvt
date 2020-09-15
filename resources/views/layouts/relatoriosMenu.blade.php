                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Relatórios <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="ListaUnica"><a href="{{ route('listaUnica.relatorio') }}">Relatório Vítimas (LISTA ÚNICA)</a> </li>
                            <li class="ListaUnica"><a href="{{ route('quadro.relatorio') }}">Relatório Quadro Múltiplo</a> </li>
                            <li class="UpSIM"><a href="{{ route('sim.relatorio') }}">Relatório SIM</a> </li>
                            <li class="UpSIH"><a href="{{ route('sih.relatorio') }}">Relatório SIH</a>
                            </li>
                            <li class="UpSIM"><a href="{{ route('sim.relatorio.pares') }}">Relatório Pares SIM</a>
                            </li>
                            <li class="UpSIH"><a href="{{ route('sih.relatorio.pares') }}">Relatório Pares SIH</a> </li>
                            <li class="UpSIM"><a href="{{ route('resultado.geral') }}">Relatório Geral</a> </li>
                            <li class="UpSIM"><a href="{{ route('resultado.indicadores') }}">Relatório de Indicadores Finais</a> </li>
                        </ul>
                    </li>