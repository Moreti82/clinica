import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  services = [
    'Desenvolvimento de Software sob medida',
    'Aplicativos Android (Kotlin/Flutter)',
    'Web Apps (Angular, React, Node.js)',
    'Aplicações Desktop',
    'Consultoria em Engenharia de Software'
  ];

  projects = [
    {
      title: 'Sistema de Gestão Clínica',
      description: 'Plataforma web para agendamentos, prontuários e financeiro com foco em produtividade.'
    },
    {
      title: 'App Android para Atendimento',
      description: 'Aplicativo móvel com notificações em tempo real e integração com APIs REST.'
    },
    {
      title: 'Dashboard Executivo',
      description: 'Painel de indicadores para tomada de decisão e monitoramento de performance.'
    }
  ];
}
