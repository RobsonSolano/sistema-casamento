#!/usr/bin/env python3
"""
Script para gerar imagens em ALTA QUALIDADE dos diagramas Mermaid
Suporta múltiplos formatos: PNG, SVG, PDF
"""

import base64
import urllib.request
import urllib.error
import os
import json
import subprocess
import sys

def check_mmdc_installed():
    """Verifica se mermaid-cli (mmdc) está instalado"""
    try:
        subprocess.run(['mmdc', '--version'], 
                      capture_output=True, 
                      check=True)
        return True
    except (subprocess.CalledProcessError, FileNotFoundError):
        return False

def mermaid_to_image_local(mermaid_file, output_file, width=3000, height=2000):
    """
    Usa mermaid-cli local (MELHOR QUALIDADE)
    Requer: npm install -g @mermaid-js/mermaid-cli
    """
    print(f"🎨 Renderizando com mermaid-cli local...")
    
    try:
        # Configuração de alta qualidade
        config = {
            "theme": "default",
            "themeVariables": {
                "fontSize": "18px",
                "fontFamily": "Arial, sans-serif"
            }
        }
        
        config_file = '/tmp/mermaid-config.json'
        with open(config_file, 'w') as f:
            json.dump(config, f)
        
        # Executar mmdc
        cmd = [
            'mmdc',
            '-i', mermaid_file,
            '-o', output_file,
            '-w', str(width),
            '-H', str(height),
            '-c', config_file,
            '-b', 'white',
            '--scale', '3'  # 3x resolution
        ]
        
        result = subprocess.run(cmd, 
                              capture_output=True, 
                              text=True,
                              timeout=30)
        
        if result.returncode == 0:
            file_size = os.path.getsize(output_file) / 1024
            print(f"✅ Salvo (LOCAL): {output_file} ({file_size:.1f} KB)")
            return True
        else:
            print(f"❌ Erro mmdc: {result.stderr}")
            return False
            
    except subprocess.TimeoutExpired:
        print(f"⏱️ Timeout ao gerar imagem")
        return False
    except Exception as e:
        print(f"❌ Erro: {e}")
        return False

def mermaid_to_svg(mermaid_file, output_file):
    """
    Gera SVG vetorial (QUALIDADE INFINITA)
    Usa mermaid.ink API
    """
    print(f"📄 Lendo {mermaid_file}...")
    
    with open(mermaid_file, 'r', encoding='utf-8') as f:
        mermaid_code = f.read()
    
    # Codificar em base64
    encoded = base64.urlsafe_b64encode(mermaid_code.encode('utf-8')).decode('utf-8')
    
    # URL da API SVG
    url = f"https://mermaid.ink/svg/{encoded}"
    
    print(f"🌐 Baixando SVG de mermaid.ink...")
    
    try:
        req = urllib.request.Request(
            url,
            headers={'User-Agent': 'Mozilla/5.0'}
        )
        
        with urllib.request.urlopen(req, timeout=20) as response:
            svg_data = response.read()
        
        with open(output_file, 'wb') as f:
            f.write(svg_data)
        
        file_size = len(svg_data) / 1024
        print(f"✅ Salvo (SVG): {output_file} ({file_size:.1f} KB)")
        return True
        
    except urllib.error.HTTPError as e:
        if e.code == 414:
            print(f"❌ Diagrama muito grande para API online")
        else:
            print(f"❌ Erro HTTP {e.code}: {e.reason}")
        return False
    except Exception as e:
        print(f"❌ Erro: {e}")
        return False

def mermaid_to_png_api(mermaid_file, output_file, scale=3):
    """
    Gera PNG via API (ALTA QUALIDADE)
    scale: 1-5 (quanto maior, melhor qualidade)
    """
    print(f"📄 Lendo {mermaid_file}...")
    
    with open(mermaid_file, 'r', encoding='utf-8') as f:
        mermaid_code = f.read()
    
    # Codificar em base64
    encoded = base64.urlsafe_b64encode(mermaid_code.encode('utf-8')).decode('utf-8')
    
    # URL da API PNG com qualidade
    url = f"https://mermaid.ink/img/{encoded}?type=png&scale={scale}&bgColor=white"
    
    print(f"🌐 Baixando PNG (scale={scale}) de mermaid.ink...")
    
    try:
        req = urllib.request.Request(
            url,
            headers={
                'User-Agent': 'Mozilla/5.0',
                'Accept': 'image/png'
            }
        )
        
        with urllib.request.urlopen(req, timeout=20) as response:
            image_data = response.read()
        
        with open(output_file, 'wb') as f:
            f.write(image_data)
        
        file_size = len(image_data) / 1024
        print(f"✅ Salvo (PNG): {output_file} ({file_size:.1f} KB)")
        return True
        
    except urllib.error.HTTPError as e:
        if e.code == 414:
            print(f"❌ Diagrama muito grande para API online")
        else:
            print(f"❌ Erro HTTP {e.code}: {e.reason}")
        return False
    except Exception as e:
        print(f"❌ Erro: {e}")
        return False

def convert_svg_to_png(svg_file, png_file, width=4000):
    """
    Converte SVG para PNG em alta resolução
    Requer: inkscape ou rsvg-convert
    """
    converters = [
        # Inkscape (melhor qualidade)
        ['inkscape', svg_file, '--export-filename', png_file, 
         f'--export-width={width}', '--export-background=white'],
        
        # rsvg-convert (alternativa)
        ['rsvg-convert', svg_file, '-o', png_file, 
         f'--width={width}', '--background-color=white']
    ]
    
    for cmd in converters:
        try:
            result = subprocess.run(cmd, 
                                  capture_output=True,
                                  timeout=30)
            if result.returncode == 0:
                file_size = os.path.getsize(png_file) / 1024
                print(f"✅ Convertido SVG→PNG: {png_file} ({file_size:.1f} KB)")
                return True
        except (FileNotFoundError, subprocess.TimeoutExpired):
            continue
    
    print("⚠️ Nenhum conversor SVG→PNG encontrado (inkscape/rsvg-convert)")
    return False

def main():
    """Gera todos os diagramas em ALTA QUALIDADE"""
    
    # Diretório atual
    current_dir = os.path.dirname(os.path.abspath(__file__))
    
    # Diagramas simplificados (cabe na API)
    diagramas_simples = [
        {
            'input': 'diagrama-er-simples.mmd',
            'output_base': 'diagrama-er-simples',
            'titulo': 'DER Simplificado'
        },
        {
            'input': 'arquitetura-simples.mmd',
            'output_base': 'arquitetura-simples',
            'titulo': 'Arquitetura Simplificada'
        }
    ]
    
    # Diagramas completos (requer local ou SVG)
    diagramas_completos = [
        {
            'input': 'diagrama-er.mmd',
            'output_base': 'diagrama-er-completo',
            'titulo': 'DER Completo (18 tabelas)'
        },
        {
            'input': 'arquitetura-sistema.mmd',
            'output_base': 'arquitetura-completo',
            'titulo': 'Arquitetura Completa'
        }
    ]
    
    print("=" * 70)
    print("🎨 GERADOR DE DIAGRAMAS - ALTA QUALIDADE")
    print("   MeuCasamento.com.br SaaS")
    print("=" * 70)
    
    # Detectar método disponível
    has_mmdc = check_mmdc_installed()
    
    if has_mmdc:
        print("\n✅ mermaid-cli (mmdc) INSTALADO - Usando renderização local")
        print("   (Melhor qualidade possível)")
    else:
        print("\n⚠️  mermaid-cli NÃO instalado - Usando API online")
        print("   📦 Para melhor qualidade, instale: npm install -g @mermaid-js/mermaid-cli")
    
    print("\n" + "=" * 70)
    
    estatisticas = {
        'svg': 0,
        'png_local': 0,
        'png_api': 0,
        'falhas': 0
    }
    
    todos_diagramas = diagramas_simples + diagramas_completos
    
    for diagrama in todos_diagramas:
        input_path = os.path.join(current_dir, diagrama['input'])
        output_base = os.path.join(current_dir, diagrama['output_base'])
        
        print(f"\n📊 {diagrama['titulo']}")
        print("-" * 70)
        
        if not os.path.exists(input_path):
            print(f"❌ Arquivo não encontrado: {input_path}")
            estatisticas['falhas'] += 1
            continue
        
        sucesso_algum = False
        
        # ESTRATÉGIA 1: Gerar SVG (sempre, qualidade infinita)
        svg_path = f"{output_base}.svg"
        if mermaid_to_svg(input_path, svg_path):
            estatisticas['svg'] += 1
            sucesso_algum = True
            
            # Tentar converter SVG para PNG de alta qualidade
            png_from_svg = f"{output_base}-hq.png"
            if convert_svg_to_png(svg_path, png_from_svg, width=4000):
                print(f"   🎯 PNG alta qualidade gerado do SVG!")
                estatisticas['png_local'] += 1
        
        # ESTRATÉGIA 2: Usar mmdc local (se disponível)
        if has_mmdc:
            png_local = f"{output_base}.png"
            if mermaid_to_image_local(input_path, png_local, width=3000, height=2500):
                estatisticas['png_local'] += 1
                sucesso_algum = True
        
        # ESTRATÉGIA 3: API online com alta qualidade (fallback)
        if not has_mmdc:
            png_api = f"{output_base}.png"
            if mermaid_to_png_api(input_path, png_api, scale=3):
                estatisticas['png_api'] += 1
                sucesso_algum = True
        
        if not sucesso_algum:
            estatisticas['falhas'] += 1
    
    # Resumo final
    print("\n" + "=" * 70)
    print("📈 ESTATÍSTICAS")
    print("=" * 70)
    print(f"✅ SVG gerados:           {estatisticas['svg']}")
    print(f"✅ PNG local (mmdc):      {estatisticas['png_local']}")
    print(f"✅ PNG API (online):      {estatisticas['png_api']}")
    print(f"❌ Falhas:                {estatisticas['falhas']}")
    print("=" * 70)
    
    # Listar arquivos gerados
    print("\n📁 ARQUIVOS GERADOS:")
    print("-" * 70)
    
    extensions = ['.svg', '.png', '-hq.png']
    arquivos_encontrados = []
    
    for diagrama in todos_diagramas:
        output_base = os.path.join(current_dir, diagrama['output_base'])
        for ext in extensions:
            file_path = f"{output_base}{ext}"
            if os.path.exists(file_path):
                size_kb = os.path.getsize(file_path) / 1024
                size_mb = size_kb / 1024
                
                if size_mb >= 1:
                    size_str = f"{size_mb:.2f} MB"
                else:
                    size_str = f"{size_kb:.1f} KB"
                
                filename = os.path.basename(file_path)
                arquivos_encontrados.append(f"   • {filename:<40} {size_str}")
    
    if arquivos_encontrados:
        for arquivo in sorted(arquivos_encontrados):
            print(arquivo)
    else:
        print("   (nenhum arquivo gerado)")
    
    print()
    print("=" * 70)
    print("💡 DICA:")
    print("   • Use arquivos .SVG para zoom infinito (recomendado!)")
    print("   • Use arquivos .PNG para apresentações/documentos")
    print("   • Arquivos -hq.png têm resolução 4000px (melhor qualidade)")
    print("=" * 70)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Interrompido pelo usuário")
        sys.exit(1)

